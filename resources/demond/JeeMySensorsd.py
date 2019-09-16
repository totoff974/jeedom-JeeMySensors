# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.

import globals
from threading import Thread, RLock
import socket
import logging
import sys
import os
import time
import signal
import json
import argparse

try:
    from jeedom.jeedom import *
except ImportError:
    print "Error: importing module jeedom.jeedom"
    sys.exit(1)

try:
    from JeeMySensors.JeeConnexion import *
except ImportError:
    print "Error: importing module JeeMySensors.JeeConnexion"
    sys.exit(1)

# lire les donnees de Jeedom en socket
def read_socket(name):
    while True:
        time.sleep(0.3)
        try:
            global JEEDOM_SOCKET_MESSAGE
            if not JEEDOM_SOCKET_MESSAGE.empty():
                message = json.loads(jeedom_utils.stripped(JEEDOM_SOCKET_MESSAGE.get()))
                if message['apikey'] != _apikey:
                    logging.error("Invalid apikey from socket : " + str(message))
                    return
                # ajoute une gateway en fonction de son type LAN ou SERIAL
                if message['cmd'] == 'addgw':
                    try:
                        gateway_id = message['gateway']
                        data = message['data']
                        if (len(gateway_id) != 0 and len(data) != 0):
                            heartbeat, type_gw, addr_gw = data.split(';', 2)
                            if not globals.KNOWN_DEVICES.has_key(gateway_id):
                                add_gw(gateway_id, heartbeat, type_gw, addr_gw)
                            else:
                                modif_gw(gateway_id, heartbeat, type_gw, addr_gw)
                    except Exception, e:
                        logging.error('[%s] Add Gateway command to MySensors error : %s' % (gateway_id.center(5, ' '), str(e)))
                # Supprime une gateway
                if message['cmd'] == 'delgw':
                    try:
                        gateway_id = message['gateway']
                        if (len(gateway_id) != 0 and globals.KNOWN_DEVICES.has_key(gateway_id)):
                            del_gw(gateway_id, 0)
                    except Exception, e:
                        logging.error('Delete Gateway command to MySensors error : '+str(e))
                # Envoi un message sur la gateway choisie
                if message['cmd'] == 'send':
                    try:
                        gateway_id = message['gateway']
                        if globals.KNOWN_DEVICES.has_key(gateway_id):
                            data = (message['data'] + '\n').encode('ascii')
                            send_mysensors(gateway_id, data)
                    except Exception, e:
                        logging.error('Send command to MySensors error : '+str(e))
        except Exception as e:
            logging.error("Exception on socket : %s" % str(e))
            time.sleep(0.3)

def add_gw(gateway_id, heartbeat, type_gw, addr_gw):
    if (len(gateway_id) != 0 and len(type_gw) != 0 and len(addr_gw) != 0):
        logging.debug('[%s] |--> Ouverture du Thread %s sur %s' % (gateway_id.center(5, ' '), type_gw, str(addr_gw)))
        if (type_gw == 'serial'):
            globals.KNOWN_DEVICES[gateway_id] = {}
            globals.KNOWN_DEVICES[gateway_id]['type'] = type_gw
            globals.KNOWN_DEVICES[gateway_id]['addr'] = addr_gw
            globals.KNOWN_DEVICES[gateway_id]['heartbeat'] = heartbeat
            jeeThreadCo[gateway_id] = mysensors_serial(gateway_id, addr_gw, int(heartbeat))
            jeeThreadCo[gateway_id].start()
        if (type_gw == 'lan'):
            globals.KNOWN_DEVICES[gateway_id] = {}
            globals.KNOWN_DEVICES[gateway_id]['type'] = type_gw
            globals.KNOWN_DEVICES[gateway_id]['addr'] = addr_gw
            globals.KNOWN_DEVICES[gateway_id]['heartbeat'] = heartbeat
            hoteport = addr_gw.split(":")
            hote, port = addr_gw.split(':', 1)
            jeeThreadCo[gateway_id] = mysensors_socket(gateway_id, hote, int(port), int(heartbeat))
            jeeThreadCo[gateway_id].start()

def modif_gw(gateway_id, heartbeat, type_gw, addr_gw):
    modif = 0
    if (len(gateway_id) != 0 and len(type_gw) != 0 and len(addr_gw) != 0):
        if (int(globals.KNOWN_DEVICES[gateway_id]['heartbeat']) != int(heartbeat)):
            modif = 1
        if (globals.KNOWN_DEVICES[gateway_id]['type'] != type_gw):
            modif = 1
        if (globals.KNOWN_DEVICES[gateway_id]['addr'] != addr_gw):
            modif = 1
        if (modif == 1):
            logging.debug('[%s] |--> Application des modifications type : %s -> heartbeat : %s -> %s' % (gateway_id.center(5, ' '), type_gw, str(heartbeat), str(addr_gw)))
            del_gw(gateway_id, 1)
            add_gw(gateway_id, heartbeat, type_gw, addr_gw)
        else:
            return

# Suppression de la gateway
def del_gw(gateway_id, modif):
    logging.debug('[%s] |--> Fermeture du Thread' % (gateway_id.center(5, ' ')))
    try:
        jeeThreadCo[gateway_id].stop()
        jeeThreadCo[gateway_id].join()
    except Exception, e:
        logging.error("Erreur sur la fermeture du port : " + str(e))
        pass
    if (modif == 0):
        del globals.KNOWN_DEVICES[gateway_id]
    logging.debug('[%s] |--> Fermeture du Thread ok.' % (gateway_id.center(5, ' ')))

def send_mysensors(gateway_id, data):
    if (globals.KNOWN_DEVICES[gateway_id]['type'] == 'serial'):
        try:
            globals.KNOWN_DEVICES[gateway_id]['objCo'].flushOutput()
            globals.KNOWN_DEVICES[gateway_id]['objCo'].flushInput()
            globals.KNOWN_DEVICES[gateway_id]['objCo'].write(data)
            logging.debug('[%s] |--> Message Serial Write : %s' % (gateway_id.center(5, ' '), data))
        except Exception, e:
            logging.error('[%s] |--> ERREUR Message Serial Write : %s' % (gateway_id.center(5, ' '), str(e)))
            pass
    if (globals.KNOWN_DEVICES[gateway_id]['type'] == 'lan'):
        try:
            globals.KNOWN_DEVICES[gateway_id]['objCo'].send(data)
            logging.debug('[%s] |--> Message Lan Write : %s' % (gateway_id.center(5, ' '), data))
        except Exception, e:
            logging.error('[%s] |--> ERREUR Lan Serial Write : %s' % (gateway_id.center(5, ' '), str(e)))
            pass

def listen():
    jeedom_socket.open()
    threading.Thread(target=read_socket, args=('socket',)).start()

# ----------------------------------------------------------------------------

def handler(signum=None, frame=None):
    logging.debug("Signal %i caught, exiting..." % int(signum))
    shutdown()

def shutdown():
    logging.debug("Shutdown")
    logging.debug("Removing PID file " + str(_pid))
    try:
        os.remove(_pid)
    except:
        pass
    try:
        jeedom_socket.close()
    except:
        pass
    try:
        if len(globals.KNOWN_DEVICES) != 0:
            for gw in globals.KNOWN_DEVICES.keys():
                if globals.KNOWN_DEVICES[gw]['type'] == 'serial':
                    _jeedom_serial[gw].close()
                if globals.KNOWN_DEVICES[gw]['type'] == 'lan':
                    _jeedom_socket[gw].close()
    except:
        pass
    logging.debug("Exit 0")
    sys.stdout.flush()
    os._exit(0)

# ----------------------------------------------------------------------------

_log_level = "info"
_socket_port = 55251
_socket_host = 'localhost'
_pid = '/tmp/jeedom/JeeMySensors/JeeMySensors.pid'
_apikey = ''
_callback = 'http://127.0.0.1:9080/plugins/JeeMySensors/core/php/JeeMySensors.inc.php'
_cycle = 0.3

parser = argparse.ArgumentParser(description='JeeMySensors Daemon for Jeedom plugin')
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--socketport", help="Socketport for server", type=str)
parser.add_argument("--sockethost", help="Sockethost for server", type=str)
parser.add_argument("--pid", help="Pid file", type=str)
parser.add_argument("--apikey", help="Apikey", type=str)
parser.add_argument("--callback", help="Callback", type=str)
args = parser.parse_args()

if args.loglevel:
	_log_level = args.loglevel
if args.socketport:
	_socket_port = args.socketport
if args.sockethost:
	_socket_host = args.sockethost
if args.pid:
	_pid = args.pid
if args.apikey:
	_apikey = args.apikey
if args.callback:
	_callback = args.callback

_socket_port = int(_socket_port)
_jeedom_serial = dict()
jeeThreadCo = dict()
jeedom_utils.set_log_level(_log_level)

logging.debug('Start demond')
logging.debug('Log level : '+str(_log_level))
logging.debug('Socket port : '+str(_socket_port))
logging.debug('Socket host : '+str(_socket_host))
logging.debug('PID file : '+str(_pid))
logging.debug('Callback : '+str(_callback))
logging.debug('Apikey : '+str(_apikey))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)

try:
    jeedom_utils.write_pid(str(_pid))
    globals.JEEDOM_COM = jeedom_com(apikey = _apikey, url = _callback, cycle = _cycle)
    if not globals.JEEDOM_COM.test():
        logging.error('Network communication issues. Please fixe your Jeedom network configuration.')
    jeedom_socket = jeedom_socket(port=_socket_port,address=_socket_host)
    listen()
except Exception,e:
    logging.error('Fatal error : '+str(e))
    shutdown()
