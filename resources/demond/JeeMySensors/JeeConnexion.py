import globals
import threading
import socket
import time

try:
    from jeedom.jeedom import *
except ImportError:
    print "Error: importing module jeedom"
    sys.exit(1)

globals.sendLock = threading.RLock()

class mysensors_socket(threading.Thread):
    def __init__(self, id_jeedom = '', ip_gw = '', port_gw = 5003, timeout=300):
        super(mysensors_socket, self).__init__()
        self.id_jeedom = str(id_jeedom)
        self.ip_gw = str(ip_gw)
        self.port_gw = int(port_gw)
        self.timeout = int(timeout)
        globals.ErrCo[self.id_jeedom] = True
        globals.enLecture[self.id_jeedom] = False

    def run(self):
        self.connexion_socket()

    def stop(self):
        logging.debug("[%s] Fermeture en cours... de %s:%s" % (self.id_jeedom.center(5, ' '), self.ip_gw, str(self.port_gw)))
        globals.ErrCo[self.id_jeedom] = False
        globals.enLecture[self.id_jeedom] = False
        try:
            globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].close()
        except:
            pass
        try:
            self.thread_isAlive.join()
        except:
            pass
        logging.debug("[%s] Fermeture ok de %s:%s" % (self.id_jeedom.center(5, ' '), self.ip_gw, str(self.port_gw)))

    # initialise la connexion vers le socket gw
    def connexion_socket(self):
        nb_tentative = 0
        while globals.ErrCo[self.id_jeedom]:
            try:
                nb_tentative += 1
                if (nb_tentative > 10):
                    logging.debug("[%s] /!\\ Plus de 10 tentatives de connexion sur %s:%s" % (self.id_jeedom.center(5, ' '), self.ip_gw, self.port_gw))
                    message = {}
                    message['gw'] = self.id_jeedom
                    message['msg_erreur'] = 0
                    globals.JEEDOM_COM.send_change_immediate({'erreur' : message});
                    self.stop()
                    break
                socket.setdefaulttimeout(self.timeout)
                logging.debug("[%s] Connexion en cours... sur %s:%s" % (self.id_jeedom.center(5, ' '), self.ip_gw, self.port_gw))
                globals.KNOWN_DEVICES[self.id_jeedom]['objCo'] = socket.create_connection((self.ip_gw, self.port_gw))
                globals.ErrCo[self.id_jeedom] = False
            except Exception,e:
                globals.ErrCo[self.id_jeedom] = True
                logging.error("[%s] Erreur de connexion pour %s:%s... : %s" % (self.id_jeedom.center(5, ' '), self.ip_gw, str(self.port_gw), str(e)))
                time.sleep(5)
                logging.error("[%s] On relance la connexion..." % (self.id_jeedom.center(5, ' ')))
            else:
                logging.debug("[%s] Connexion ok." % (self.id_jeedom.center(5, ' ')))
                self.thread_isAlive = threading.Thread(target=self.check_isAlive)
                self.thread_isAlive.start()
                globals.enLecture[self.id_jeedom] = True
                self.lecture_donnees()

    # lecture des donnees recues du socket gw
    def lecture_donnees(self):
        logging.debug("[%s] Lancement de la boucle de lecture" % (self.id_jeedom.center(5, ' ')))
        while globals.enLecture[self.id_jeedom]:
            try:
                message = bytes(globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].recv(120)).decode('ascii')
                for msg in message.split('\n'):
                    send_to_jeedom('LAN', self.id_jeedom, msg).start()
            except socket.error as e:
                if globals.ErrCo[self.id_jeedom] == False and globals.enLecture[self.id_jeedom] == False:
                    break
                else:
                    if str(e) == "timed out":
                        pass
                    else:
                        logging.error("[%s] Erreur analyse du message : %s" % (self.id_jeedom.center(5, ' '), str(e)))

    # heartbeat de controle que le gw est accessible
    def check_isAlive(self):
        logging.debug("[%s] Lancement du Heartbeat - %s:%s toutes les %s secondes..." % (self.id_jeedom.center(5, ' '), self.ip_gw, str(self.port_gw), str(self.timeout)))
        req = "0;255;3;0;18;\n"
        globals.heartbeat[self.id_jeedom] = time.time()
        nbr_boucle = 0
        # heartbeat_timeout ne peut pas etre inferieur a 10
        if self.timeout < 10:
            self.timeout = 11
        while globals.enLecture[self.id_jeedom]:
            if nbr_boucle < (self.timeout / 10):
                nbr_boucle = nbr_boucle + 1
                time.sleep(10)
            else:
                nbr_boucle = 0
                try:
                    self.socketInstance.send(req)
                except:
                    pass
                finally:
                    now = time.time()
                    diff = now - globals.heartbeat[self.id_jeedom]
                    if diff > (self.timeout + 2):
                        globals.ErrCo[self.id_jeedom] = True
                        logging.error("[%s] Gateway indisponible depuis plus de %s secondes..." % (self.id_jeedom.center(5, ' '), str(self.timeout)))
                        self.connexion_socket()

class mysensors_serial(threading.Thread):
    def __init__(self, id_jeedom = '', port_gw = None, timeout=10):
        super(mysensors_serial, self).__init__()
        self.id_jeedom = str(id_jeedom)
        self.port_gw = str(port_gw)
        self.timeout = int(timeout)
        globals.ErrCo[self.id_jeedom] = True
        globals.enLecture[self.id_jeedom] = False

    def run(self):
        self.connexion_serial()

    def stop(self):
        logging.debug("[%s] Fermeture en cours... de %s" % (self.id_jeedom.center(5, ' '), self.port_gw))
        globals.ErrCo[self.id_jeedom] = False
        globals.enLecture[self.id_jeedom] = False
        try:
            globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].close()
        except:
            pass
        logging.debug("[%s] Fermeture ok de %s" % (self.id_jeedom.center(5, ' '), self.port_gw))

    # initialise la connexion vers le serial gw
    def connexion_serial(self):
        while globals.ErrCo[self.id_jeedom]:
            try:
                logging.debug("[%s] Connexion en cours... sur %s" % (self.id_jeedom.center(5, ' '), self.port_gw))
                globals.KNOWN_DEVICES[self.id_jeedom]['objCo'] = jeedom_serial(device=self.port_gw,rate=115200,timeout=self.timeout)
                globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].open()
                globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].flushOutput()
                globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].flushInput()
                globals.ErrCo[self.id_jeedom] = False
            except Exception,e:
                globals.ErrCo[self.id_jeedom] = True
                logging.error("[%s] Erreur de connexion pour %s... : %s" % (self.id_jeedom.center(5, ' '), self.port_gw, str(e)))
                time.sleep(5)
                logging.error("[%s] On relance la connexion..." % (self.id_jeedom.center(5, ' ')))
            else:
                logging.debug("[%s] Connexion ok." % (self.id_jeedom.center(5, ' ')))
                globals.enLecture[self.id_jeedom] = True
                self.lecture_donnees()

    # lecture des donnees recues du serial gw
    def lecture_donnees(self):
        logging.debug("[%s] Lancement de la boucle de lecture" % (self.id_jeedom.center(5, ' ')))
        while globals.enLecture[self.id_jeedom]:
            time.sleep(0.02)
            message = None
            try:
                byte = globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].read()
            except Exception, e:
                if str(e) == '[Errno 5] Input/output error':
                    logging.error("Exit 1 because this exeption is fatal")
                    self.stop()
            try:
                if byte:
                    message = (byte + globals.KNOWN_DEVICES[self.id_jeedom]['objCo'].readline()).decode('ascii')
                    if message.endswith('\n'):
                        send_to_jeedom('SERIAL', self.id_jeedom, message).start()
            except Exception,e:
                logging.error("[%s] Erreur analyse du message : %s" % (self.id_jeedom.center(5, ' '), str(e)))

###########################
### diverses class communes
class send_to_jeedom(threading.Thread):
    def __init__(self, typeOfco = '', id_jeedom = '', messages = ''):
        super(send_to_jeedom, self).__init__()
        self.typeOfco = typeOfco
        self.id_jeedom = id_jeedom
        self.messages = messages

    def run(self):
        self.check_and_send()

    # Envoyer les donnees vers Jeedom en socket
    def check_and_send(self):
        message = {}
        message['typeOfco'] = self.typeOfco
        message['gw'] = self.id_jeedom
        msg = self.messages.split(';', 5)
        try:
            node_id = int(msg[0])
            if (0 <= node_id and node_id <= 255):
                message['node-id'] = node_id
                child_sensor_id = int(msg[1])
                if (0 <= child_sensor_id and child_sensor_id <= 255):
                    message['child-sensor-id'] = child_sensor_id
                    command = int(msg[2])
                    if (command == 0):
                        message['command'] = command
                        ack = int(msg[3])
                        if (0 <= ack and ack <= 1):
                            message['ack'] = ack
                            type = int(msg[4])
                            if (type == 22):
                                globals.heartbeat[self.id_jeedom] = time.time()
                            if (0 <= type and type <= 39):
                                message['type'] = type
                                payload = msg[5]
                                message['payload'] = payload
                            else:
                                return
                        else:
                            message = {}
                    elif (command == 1 or command == 2):
                        message['command'] = command
                        ack = int(msg[3])
                        if (0 <= ack and ack <= 1):
                            message['ack'] = ack
                            type = int(msg[4])
                            if (type == 22):
                                globals.heartbeat[self.id_jeedom] = time.time()
                            if (0 <= type and type <= 56):
                                message['type'] = type
                                payload = msg[5]
                                message['payload'] = payload
                            else:
                                return
                        else:
                            return
                    elif (command == 3):
                        message['command'] = command
                        ack = int(msg[3])
                        if (0 <= ack and ack <= 1):
                            message['ack'] = ack
                            type = int(msg[4])
                            if (type == 22):
                                globals.heartbeat[self.id_jeedom] = time.time()
                            if (0 <= type and type <= 28):
                                message['type'] = type
                                payload = msg[5]
                                message['payload'] = payload
                            else:
                                return
                        else:
                            return
                    elif (command == 4):
                        message['command'] = command
                        ack = int(msg[3])
                        if (0 <= ack and ack <= 1):
                            message['ack'] = ack
                            type = int(msg[4])
                            if (type == 22):
                                globals.heartbeat[self.id_jeedom] = time.time()
                            if (0 <= type and type <= 255):
                                message['type'] = type
                                payload = msg[5]
                                message['payload'] = payload
                            else:
                                return
                        else:
                            return
                    else:
                        return
                else:
                    return
            else:
                return
        except Exception, e:
            pass
        else:
            try:
                with globals.sendLock:
                    time.sleep(0.1)
                    globals.JEEDOM_COM.send_change_immediate({'message' : message});
                    #globals.JEEDOM_COM.add_changes('message', message)
            except Exception, e:
                pass
        return
