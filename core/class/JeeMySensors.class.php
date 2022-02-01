<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
include_file('core', 'JeeMySensors', 'config', 'JeeMySensors');
include_file('core', 'JeeMySensors', 'api', 'JeeMySensors');

class JeeMySensors extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */
    public static function dependancy_info() {
        $return = array();
        $return['progress_file'] = jeedom::getTmpFolder('JeeMySensors') . '/dependance';
        if (exec(system::getCmdSudo() . system::get('cmd_check') . '-E "python3\-serial|python3\-request|python3\-pyudev" | wc -l') >= 3) {
            $return['state'] = 'ok';
        } else {
            $return['state'] = 'nok';
        }
        return $return;
    }
    public static function dependancy_install() {
        log::remove(__CLASS__ . '_update');
        return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('JeeMySensors') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
    }

    public static function deamon_info() {
        $return = array();
        $return['log'] = 'JeeMySensors';
        $return['state'] = 'nok';
        $pid_file = jeedom::getTmpFolder('JeeMySensors') . '/JeeMySensors.pid';
        if (file_exists($pid_file)) {
            $pid = trim(file_get_contents($pid_file));
            if (is_numeric($pid) && posix_getsid($pid)) {
                $return['state'] = 'ok';
            } else {
                shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null;rm -rf ' . $pid_file . ' 2>&1 > /dev/null;');
            }
        }
        $return['launchable'] = 'ok';
        return $return;
    }

    public static function deamon_start() {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        $JeeMySensors_path = realpath(dirname(__FILE__) . '/../../resources/demond');
        $cmd = '/usr/bin/python ' . $JeeMySensors_path . '/JeeMySensorsd.py';
        $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel('JeeMySensors'));
        $cmd .= ' --socketport ' . config::byKey('socketport', 'JeeMySensors');
        $cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/JeeMySensors/core/php/JeeMySensors.inc.php';
        $cmd .= ' --apikey ' . jeedom::getApiKey('JeeMySensors');
        $cmd .= ' --pid ' . jeedom::getTmpFolder('JeeMySensors') . '/JeeMySensors.pid';

        log::add('JeeMySensors', 'info', 'Lancement démon JeeMySensorsd : ' . $cmd);
        exec($cmd . ' >> ' . log::getPathToLog('JeeMySensors') . ' 2>&1 &');
        $i = 0;
        while ($i < 30) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
            sleep(1);
            $i++;
        }
        if ($i >= 30) {
            log::add('JeeMySensors', 'error', 'Impossible de lancer le démon JeeMySensors, vérifiez le log', 'unableStartDeamon');
            return false;
        }
        message::removeAll('JeeMySensors', 'unableStartDeamon');
        sleep(2);
        self::sendIdToDeamon();
        log::add('JeeMySensors', 'info', 'Démon JeeMySensors lancé');
        return true;
    }

    public static function deamon_stop() {
        $pid_file = jeedom::getTmpFolder('JeeMySensors') . '/JeeMySensors.pid';
        if (file_exists($pid_file)) {
            $pid = intval(trim(file_get_contents($pid_file)));
            system::kill($pid);
        }
        system::kill('JeeMySensorsd.py');
        system::fuserk(config::byKey('socketport', 'JeeMySensors'));
        sleep(1);
    }

    public static function sendIdToDeamon() {
        foreach (self::byType('JeeMySensors') as $eqLogic) {
            $value = explode("-", $eqLogic->getLogicalId());
            $select = $value[0];
            if ($select === 'GW') {
                $gateway = $eqLogic->getId();
                $hearbeat = intval($eqLogic->getConfiguration('timeout'));
                if ($eqLogic->getIsEnable() == 1) {
                    if ($eqLogic->getConfiguration('type_gw') === 'serial') {
                        $port_type = 'port_gw';
                    }
                    if ($eqLogic->getConfiguration('type_gw') === 'lan') {
                        $port_type = 'ip_gw';
                    }
                    $values = $hearbeat . ';' . $eqLogic->getConfiguration('type_gw') . ';' . $eqLogic->getConfiguration($port_type);
                    $command = 'addgw';
                }
                if ($eqLogic->getIsEnable() == 0) {
                    $values = null;
                    $command = 'delgw';
                }
                self::sendSocket($gateway, $values, $command);
            }
        }
    }

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        $this->setConfiguration('autoInclude', 1);
    }

    public function postInsert() {

    }

    public function preSave() {
        if ($this->getConfiguration('role') === '') {
            $this->setConfiguration('role', 'Gateway');
            $this->setConfiguration('timeout', 60);
            $this->setConfiguration('modeInclude', 1);
            $this->setConfiguration('autoInclude', 0);
        }
    }

    public function postSave() {
        if ($this->getConfiguration('role') === '') {
            $this->setConfiguration('role', 'Gateway');
            $this->setConfiguration('timeout', 60);
            $this->setConfiguration('modeInclude', 1);
            $this->setConfiguration('autoInclude', 0);
        }
    }

    public function preUpdate() {

    }

    public function postUpdate() {
        if ($this->getConfiguration('role') === 'Gateway' && !empty($this->getConfiguration('type_gw')) && (!empty($this->getConfiguration('port_gw')) || !empty($this->getConfiguration('ip_gw')))) {
            $gateway = $this->getId();
            $hearbeat = intval($this->getConfiguration('timeout'));
            if ($this->getIsEnable() == 1) {
                if ($this->getConfiguration('type_gw') === 'serial') {
                    $port_type = 'port_gw';
                }
                if ($this->getConfiguration('type_gw') === 'lan') {
                    $port_type = 'ip_gw';
                }
                $values = $hearbeat . ';' . $this->getConfiguration('type_gw') . ';' . $this->getConfiguration($port_type);
                $command = 'addgw';
            }
            if ($this->getIsEnable() == 0) {
                $values = null;
                $command = 'delgw';
            }
            $this->sendSocket($gateway, $values, $command);
        }
    }

    public function preRemove() {
        $value = explode("-", $this->getLogicalId());
        $select = $value[0];
        if ($select === 'GW') {
            $gateway = $this->getId();
            $this->sendSocket($gateway, null, 'delgw');
            self::cleanNode($gateway);
        }
    }

    public function postRemove() {

    }

    public static function cleanNode($gateway) {
        foreach (self::byType('JeeMySensors') as $eqLogic) {
            $value = explode("-", $eqLogic->getLogicalId());
            $select = $value[0];
            if ($select === 'Sensor' || $select === 'Node') {
                $id_node_gw = $eqLogic->getConfiguration('id_node_gw');
                if ($id_node_gw === $gateway) {
                    $eqLogic->remove();
                }
            }
        }
    }
    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
    // Gestion des erreurs de python
    public static function gestion_Erreur($id_gw_jeedom, $msg_erreur) {
        $idlogic = self::byId($id_gw_jeedom, 'JeeMySensors');
            if (is_object($idlogic)) {
                log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . ']     \___ On désactive l\'équipement');
                $idlogic->setIsEnable(0);
                $idlogic->save();
            }
    }

    // Actions sur Mode Presentation
    public static function mode_Presentation($mode, $typeOfco, $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        $idjeedom = self::byId($id_gw_jeedom, 'JeeMySensors');
        if (is_object($idjeedom)) {
            // Presentation du gateway
            if ($id_node == '0' AND $id_sensor == '255') {
                JeeMySensors::node_Presentation($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
            }
            // Presentation des Nodes et Sensors
            else {
                if ($idjeedom->getConfiguration('autoInclude') == 1 OR $idjeedom->getConfiguration('modeInclude') == 1) {
                    $idjeedom->setConfiguration('modeInclude', 0);
                    $idjeedom->save();
                    switch ($id_sensor) {
                        // Presentation du Node Version
                        case '255':
                            JeeMySensors::node_Presentation($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
                            break;
                        // Presentation des autres sensors
                        default:
                            JeeMySensors::sensor_Presentation($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
                            break;
                    }
                    event::add('JeeMySensors::includeDevice', $idjeedom->getId());
                }
            }
        }
    }

    // Activation inclusion manuellement
    public static function inclusionMode($id_gw_jeedom, $state) {
        // node-id ; child-sensor-id ; command ; ack ; type ; payload
        $action = array (
            '0',                                            // id gateway
            '255',                                          // id sensor
            '3',                                            // 3 -> internal
            '0',                                            // 0 -> pas de retour ACK
            '5',                                            // 5 -> inclusion mode
            $state                                          // payload
        );
        $value = implode(";", $action);
        log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Activation du mode inclusion');
        self::sendSocket($id_gw_jeedom, $value, 'send');
        $idjeedom = self::byId($id_gw_jeedom, 'JeeMySensors');
        if (is_object($idjeedom)) {
            $idjeedom->setConfiguration('modeInclude', 1);
            $idjeedom->save();
        }
    }

    // Actions sur Mode Set
    public static function mode_Set($mode, $typeOfco, $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        global $_api;
        $idlogic_sensor = 'Sensor-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic_etat = 'etat-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic = self::byLogicalId($idlogic_sensor, 'JeeMySensors');
        if (is_object($idlogic)) {
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Message reçu sur le Gateway');
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . ']     \___ Node : ' . $id_node . " et sensor ID : " . $id_sensor);
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . ']         \___ Action : ' . $_api['C'][$command] . ' -> ' . $payload . ' pour ' . $_api['V'][$type][0]);
            foreach ($idlogic->getCmd() as $cmd) {
              if ($cmd->getLogicalId() === $idlogic_etat ) {
                $cmd->event($payload);
                $cmd->save();
              }
            }
        }
    }

    // Actions sur Mode Req
    public static function mode_Req($mode, $typeOfco, $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {

    }

    // Actions sur Mode Internal
    public static function mode_Internal($mode, $typeOfco, $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        switch ($type) {
            // niveau de la batterie (en pourcentage de 0 à 100).
            case '0':
                JeeMySensors::i_BATTERY_LEVEL($id_gw_jeedom, $id_node, $id_sensor, $payload);
                break;
            // demander l'heure actuelle au contrôleur à l'aide de ce message. Le temps sera rapporté comme les secondes depuis 1970
            case '1':

                break;
            // demander la version de la passerelle au contrôleur.
            case '2':

                break;
            // demander un identifiant de noeud unique au contrôleur.
            case '3':

                break;
            // réponse Id retour au noeud. La charge contient l'id du nœud.
            case '4':

                break;
            // démarrer / arrêter le mode d’inclusion du contrôleur (1 = démarrer, 0 = arrêter).
            case '5':

                break;
            // demande de configuration du noeud. Répondez avec (M) etric ou (I) au dos du capteur.
            case '6':
                JeeMySensors::i_CONFIG($id_gw_jeedom, $id_node, $id_sensor, $payload);
                break;
            // Lorsqu'un capteur démarre, il envoie une requête de recherche à tous les nœuds voisins. Ils répondent avec un I_FIND_PARENT_RESPONSE.
            case '7':

                break;
            // Répondre au type de message à la demande I_FIND_PARENT.
            case '8':

                break;
            // Envoyé par la passerelle au contrôleur pour enregistrer un message dans le journal de suivi
            case '9':

                break;
            // Un message qui peut être utilisé pour transférer des capteurs enfants (à partir de la table de routage EEPROM) d'un noeud répétitif.
            case '10':

                break;
            // Nom d'esquisse facultatif pouvant être utilisé pour identifier le capteur dans l'interface graphique du contrôleur
            case '11':
                JeeMySensors::i_SKETCH_NAME($id_gw_jeedom, $id_node, $id_sensor, $payload);
                break;
            // Version d'esquisse facultative pouvant être signalée pour garder une trace de la version du capteur dans l'interface graphique du contrôleur.
            case '12':
                JeeMySensors::i_SKETCH_VERSION($id_gw_jeedom, $id_node, $id_sensor, $payload);
                break;
            // Utilisé par les mises à jour du micrologiciel de l'OTA. Demande au nœud de redémarrer.
            case '13':

                break;
            // Envoyer par passerelle au contrôleur lorsque le démarrage est terminé.
            case '14':
                JeeMySensors::add_Gateway($typeOfco, $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
                break;
            // Fournit les préférences liées à la signature (le premier octet est la version de préférence).
            case '15':

                break;
            // Utilisé entre les capteurs lors de la demande de nonce.
            case '16':

                break;
            // Utilisé entre les capteurs pour la réponse nonce.
            case '17':

                break;
            // Demande de pulsation
            case '18':

                break;
            // Message de présentation
            case '19':

                break;
            // Demande de découverte
            case '20':

                break;
            // Découvrez la réponse
            case '21':

                break;
            // Réponse de heartbeat
            case '22':

                break;
            // Le noeud est verrouillé (raison dans string-payload)
            case '23':

                break;
            // Ping envoyé au nœud, compteur de sauts incrémentiels de charge utile
            case '24':

                break;
            // En réponse à ping, renvoyé à l'expéditeur, compteur de sauts incrémentiels de charge utile
            case '25':

                break;
            // Enregistrez votre demande à GW
            case '26':

                break;
            // Enregistrer la réponse de GW
            case '27':

                break;
            // Message de débogage
            case '28':

                break;
        }
    }
    /*     * *******************FONCTIONS PRESENTATION*********************** */

    // id_sensor : 255 - node informations
    public static function node_Presentation($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        global $_api;
        $idjeedom = self::byId($id_gw_jeedom, 'JeeMySensors');
        if (is_object($idjeedom)) {
            // modification de la version pour le gateway
            if ($idjeedom->getConfiguration('id_node') === $id_node && $idjeedom->getConfiguration('role') === 'Gateway') {
                $idlogic = 'GW-' . $id_gw_jeedom . '-' . $id_node;
                $modif = 0;
                log::add('JeeMySensors', 'info', '     \___ LogicalId Node GW: ' . $idlogic . ' existe.');
                log::add('JeeMySensors', 'info', '     \___ Vérification des modifications de la version.');
                if (version_compare($idjeedom->getConfiguration('version'), $payload, '!=') === True) {
                  log::add('JeeMySensors', 'info', '         \___ Modification de la version : ' . $idjeedom->getConfiguration('version') . " en -> " . $payload);
                  $idjeedom->setConfiguration('version', $payload);
                  if ($modif !== 1) { $modif = 1; }
                }
                if ($modif !== 1) {
                    log::add('JeeMySensors', 'info', '         \___ Pas de modification du paramètrage de la version.');
                }
                else {
                    $idjeedom->save();
                }
            }
            // Ajout du node
            else {
                $node = 'Node-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
                $logicalid_node = self::byLogicalId($node, 'JeeMySensors');
                $type_S_nom = $_api['S'][$type][0];
                $type_nom = $_api['S'][$type][1];
                if (is_object($logicalid_node)) {
                    $modif = 0;
                    log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Démarrage du Node terminé :');
                    log::add('JeeMySensors', 'info', '     \___ LogicalId Node: ' . $node . ' existe.');
                    log::add('JeeMySensors', 'info', '     \___ Vérification des modifications du paramétrage.');
                    if ($logicalid_node->getLogicalId() !== $node) {
                        log::add('JeeMySensors', 'info', '         \___ Modification du LogicalId : ' . $logicalid_node->getLogicalId() . ' en -> ' . $node);
                        $logicalid_node->setLogicalId($node);
                        if ($modif !== 1) { $modif = 1; }
                    }
                    if ($logicalid_node->getConfiguration('id_node') !== $id_node) {
                        log::add('JeeMySensors', 'info', '         \___ Modification id node : ' . $logicalid_node->getConfiguration('id_node') . ' en -> ' . $id_node);
                        $logicalid_node->setConfiguration('id_node', $id_node);
                        if ($modif !== 1) { $modif = 1; }
                    }
                    if ($logicalid_node->getConfiguration('id_sensor') !== $id_sensor) {
                        log::add('JeeMySensors', 'info', '         \___ Modification id sensor : ' . $logicalid_node->getConfiguration('id_sensor') . ' en -> ' . $id_sensor);
                        $logicalid_node->setConfiguration('id_sensor', $id_sensor);
                        if ($modif !== 1) { $modif = 1; }
                    }
                    if ($logicalid_node->getConfiguration('id_role') !== $type_S_nom) {
                        log::add('JeeMySensors', 'info', '         \___ Modification du rôle : ' . $logicalid_node->getConfiguration('role') . ' en -> ' . $type_nom);
                        $logicalid_node->setConfiguration('id_role', $type_S_nom);
                        $logicalid_node->setConfiguration('role', $type_nom);
                        if ($modif !== 1) { $modif = 1; }
                    }
                    if (version_compare($logicalid_node->getConfiguration('version'), $payload, '!=') === True) {
                      log::add('JeeMySensors', 'info', '         \___ Modification de la version : ' . $logicalid_node->getConfiguration('version') . " en -> " . $payload);
                      $logicalid_node->setConfiguration('version', $payload);
                      if ($modif !== 1) { $modif = 1; }
                    }
                    if ($modif !== 1) {
                        log::add('JeeMySensors', 'info', '         \___ Pas de modification du paramètrage de base.');
                    }
                    else {
                        $logicalid_node->save();
                    }
                }
                else {
                    log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Ajout du Node :');
                    log::add('JeeMySensors', 'info', '     \___ LogicalId Node : ' . $node);
                    $JMS = new JeeMySensors();
                    $JMS->setEqType_name('JeeMySensors');
                    $JMS->setName('Node-' . $id_node . ':' . $id_gw_jeedom);
                    $JMS->setLogicalId($node);
                    $JMS->setConfiguration('id_node_gw', $id_gw_jeedom);
                    $JMS->setConfiguration('id_node', $id_node);
                    $JMS->setConfiguration('id_sensor', $id_sensor);
                    $JMS->setConfiguration('id_role', $type_S_nom);
                    $JMS->setConfiguration('role', $type_nom);
                    $JMS->setConfiguration('version', $payload);
                    $JMS->setIsEnable(0);
                    $JMS->save();
                }
            }
        }
    }

    // id_sensor : 0 à 254 - add node sensor
    public static function sensor_Presentation($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        global $_api;
        $idlogic_sensor = 'Sensor-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic = self::byLogicalId($idlogic_sensor, 'JeeMySensors');
        $type_S_nom = $_api['S'][$type][0];
        $type_nom = $_api['S'][$type][1];
        $logicalid_node = self::byLogicalId('Node-' . $id_node . '-255:' . $id_gw_jeedom, 'JeeMySensors');
        // si le sensor existe -> verification des modifications du parametrage
        if (is_object($idlogic)) {
            $modif = 0;
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Démarrage du Sensor terminé :');
            log::add('JeeMySensors', 'info', '     \___ Node : ' . $id_node . ' et sensor ID : ' . $id_sensor . ' existe.');
            log::add('JeeMySensors', 'info', '     \___ Vérification des modifications du paramétrage.');
            if ($idlogic->getConfiguration('id_role') !== $type_S_nom) {
                log::add('JeeMySensors', 'info', '         \___ Modification du rôle : ' . $idlogic->getConfiguration('role') . ' en -> ' . $type_nom);
                $idlogic->setConfiguration('id_role', $type_S_nom);
                $idlogic->setConfiguration('role', $type_nom);
                $idlogic->save();
                log::add('JeeMySensors', 'info', '         \___ Suppression des anciennes commandes / infos');
                self::removeCommande($idlogic);
                log::add('JeeMySensors', 'info', '         \___ Ajout des commandes / infos pour le type : ' . $type_nom);
                $idlogic->autoAjoutCommande($idlogic->getId(), $type_S_nom);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($modif !== 1) {
                log::add('JeeMySensors', 'info', '         \___ Pas de modification du paramètrage.');
            }
            else {
                $idlogic->save();
            }
        }
        else {
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Ajout du Sensor :');
            log::add('JeeMySensors', 'info', '     \___ Node : ' . $id_node . ' et sensor ID : ' . $id_sensor . ' -> de type : ' . $type_nom);
            $JMS = new JeeMySensors();
            $JMS->setEqType_name('JeeMySensors');
            $JMS->setName('Sensor-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom);
            $JMS->setLogicalId($idlogic_sensor);
            $JMS->setConfiguration('id_node_gw', $id_gw_jeedom);
            $JMS->setConfiguration('id_node', $id_node);
            $JMS->setConfiguration('id_sensor', $id_sensor);
            $JMS->setConfiguration('id_role', $type_S_nom);
            $JMS->setConfiguration('role', $type_nom);
            $JMS->setIsEnable(1);
            $JMS->save();
            log::add('JeeMySensors', 'info', '     \___ Ajout des commandes / infos pour le type : ' . $type_nom);
            $short_id = $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
            $JMS->autoAjoutCommande($short_id, $type_S_nom);
        }
    }
    /*     * *********************FONCTIONS INTERNAL************************* */

    // type : 0 - I_BATTERY_LEVEL
    public static function i_BATTERY_LEVEL($id_gw_jeedom, $id_node, $id_sensor, $payload) {
        $node = 'Node-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $logicalid_node = self::byLogicalId($node, 'JeeMySensors');
        if (is_object($logicalid_node)) {
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Remontée de l\'état de la batterie pour : '. $node . ' :');
            log::add('JeeMySensors', 'info', '     \___ Etat de la batterie : ' . $payload . '%');
            $logicalid_node->batteryStatus($payload);
            $logicalid_node->setStatus('lastCommunication', date('Y-m-d H:i:s'));
            $logicalid_node->setConfiguration('node_batterie', $payload);
            $logicalid_node->save();
        }
    }

    // type : 6 - I_CONFIG
    public static function i_CONFIG($id_gw_jeedom, $id_node, $id_sensor, $payload) {
        $node = 'Node-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $logicalid_node = self::byLogicalId($node, 'JeeMySensors');
        if (is_object($logicalid_node)) {
            $modif = 0;
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Détection de la configuration du Sketch sur '. $node . ' :');
            log::add('JeeMySensors', 'info', '     \___ Vérification des modifications du paramétrage.');
            if ($logicalid_node->getConfiguration('sketch_config') !== $payload) {
                log::add('JeeMySensors', 'info', '         \___ Modification de la configuration du Sketch : ' . $logicalid_node->getConfiguration('sketch_config') . ' en -> ' . $payload);
                $logicalid_node->setConfiguration('sketch_config', $payload);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($modif !== 1) {
                log::add('JeeMySensors', 'info', '         \___ Pas de modification de la configuration du Sketch.');
            }
            else {
                $logicalid_node->save();
            }
        }
    }

    // type : 11 - I_SKETCH_NAME
    public static function i_SKETCH_NAME($id_gw_jeedom, $id_node, $id_sensor, $payload) {
        $node = 'Node-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $logicalid_node = self::byLogicalId($node, 'JeeMySensors');
        if (is_object($logicalid_node)) {
            $modif = 0;
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Détection du nom du Sketch sur '. $node . ' :');
            log::add('JeeMySensors', 'info', '     \___ Vérification des modifications du paramétrage.');
            if ($logicalid_node->getConfiguration('sketch_name') !== $payload) {
                log::add('JeeMySensors', 'info', '         \___ Modification du nom du Sketch : ' . $logicalid_node->getConfiguration('sketch_name') . ' en -> ' . $payload);
                $logicalid_node->setConfiguration('sketch_name', $payload);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($modif !== 1) {
                log::add('JeeMySensors', 'info', '         \___ Pas de modification du nom du Sketch.');
            }
            else {
                $logicalid_node->save();
            }
        }
    }

    // type : 12 - I_SKETCH_VERSION
    public static function i_SKETCH_VERSION($id_gw_jeedom, $id_node, $id_sensor, $payload) {
        $node = 'Node-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $logicalid_node = self::byLogicalId($node, 'JeeMySensors');
        if (is_object($logicalid_node)) {
            $modif = 0;
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Détection de la version du Sketch sur '. $node . ' :');
            log::add('JeeMySensors', 'info', '     \___ Vérification des modifications du paramétrage.');
            if ($logicalid_node->getConfiguration('sketch_version') !== $payload) {
                log::add('JeeMySensors', 'info', '         \___ Modification de la version du Sketch : ' . $logicalid_node->getConfiguration('sketch_version') . ' en -> ' . $payload);
                $logicalid_node->setConfiguration('sketch_version', $payload);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($modif !== 1) {
                log::add('JeeMySensors', 'info', '         \___ Pas de modification de la version du Sketch.');
            }
            else {
                $logicalid_node->save();
            }
        }
    }

    // type : 14 - Ajout du gateway
    public static function add_Gateway($typeOfco, $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        $idjeedom = self::byId($id_gw_jeedom, 'JeeMySensors');
        $idlogic = 'GW-' . $id_gw_jeedom . '-' . $id_node;
        if (is_object($idjeedom)) {
            $modif = 0;
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Démarrage du Gateway terminé :');
            log::add('JeeMySensors', 'info', '     \___ LogicalId Node GW: ' . $idlogic . ' existe.');
            log::add('JeeMySensors', 'info', '     \___ Vérification des modifications du paramétrage.');
            if ($idjeedom->getLogicalId() !== $idlogic) {
                log::add('JeeMySensors', 'info', '         \___ Modification du LogicalId : ' . $idjeedom->getLogicalId() . ' en -> ' . $idlogic);
                $idjeedom->setLogicalId($idlogic);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($idjeedom->getConfiguration('id_node') !== $id_node) {
                log::add('JeeMySensors', 'info', '         \___ Modification id node : ' . $idjeedom->getConfiguration('id_node') . ' en -> ' . $id_node);
                $idjeedom->setConfiguration('id_node', $id_node);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($idjeedom->getConfiguration('role') !== 'Gateway') {
                log::add('JeeMySensors', 'info', '         \___ Modification du rôle : ' . $idjeedom->getConfiguration('role') . ' en -> Gateway');
                $idjeedom->setConfiguration('role', 'Gateway');
                if ($modif !== 1) { $modif = 1; }
            }
            if ($idjeedom->getConfiguration('type_co') !== $typeOfco) {
                log::add('JeeMySensors', 'info', '         \___ Modification du type de connexion : ' . $idjeedom->getConfiguration('type_co') . ' en -> ' . $typeOfco);
                $idjeedom->setConfiguration('type_co', $typeOfco);
                if ($modif !== 1) { $modif = 1; }
            }
            if ($modif !== 1) {
                log::add('JeeMySensors', 'info', '         \___ Pas de modification du paramètrage de base.');
            }
            else {
                $idjeedom->save();
            }
        }
        else {
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Ajout du Gateway Automatique :');
            log::add('JeeMySensors', 'info', '     \___ LogicalId Node GW: ' . $idlogic);
            $JMS = new JeeMySensors();
            $JMS->setEqType_name('JeeMySensors');
            $JMS->setName('Gateway');
            $JMS->setLogicalId($idlogic);
            $JMS->setConfiguration('id_node', $id_node);
            $JMS->setConfiguration('role', 'Gateway');
            $JMS->setConfiguration('type_co', $typeOfco);
            $JMS->setConfiguration('timeout', 10);
            $JMS->setConfiguration('modeInclude', 1);
            $JMS->setConfiguration('autoInclude', 0);
            $JMS->setIsEnable(1);
            $JMS->save();
        }
    }

    /*     * *********************DIVERSES FONCTIONS************************* */

    // Pour envoyer message en socket
    public static function sendSocket($gateway, $value, $command) {
        $message = trim(json_encode(array('apikey' => jeedom::getApiKey('JeeMySensors'), 'cmd' => $command, 'gateway' => $gateway, 'data' => $value)));
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($socket, '127.0.0.1', config::byKey('socketport', 'JeeMySensors'));
        socket_write($socket, trim($message), strlen(trim($message)));
        socket_close($socket);
    }

    // Ajout des commandes et ou infos par défaut
    public function autoAjoutCommande($idlogic_node, $type_S_nom) {
        global $listCmdJeeMySensors;
        foreach ($listCmdJeeMySensors[$type_S_nom] as $cmd) {
           if (cmd::byEqLogicIdCmdName($this->getId(), $cmd['name']))
                return;
           if ($cmd) {
                $JeeMySensorsCmd = new JeeMySensorsCmd();
                $JeeMySensorsCmd->setName(__($cmd['name'], __FILE__));
                $JeeMySensorsCmd->setEqLogic_id($this->id);
                $JeeMySensorsCmd->setLogicalId($cmd['logicalId'] . '-' . $idlogic_node);
                $JeeMySensorsCmd->setType($cmd['type']);
                $JeeMySensorsCmd->setSubType($cmd['subType']);
                $JeeMySensorsCmd->setOrder($cmd['order']);
                $JeeMySensorsCmd->setIsVisible($cmd['isVisible']);
                $JeeMySensorsCmd->setDisplay('generic_type', $cmd['generic_type']);
                $JeeMySensorsCmd->setDisplay('forceReturnLineAfter', $cmd['forceReturnLineAfter']);
                $JeeMySensorsCmd->setTemplate('dashboard', $cmd['templateDas']);
                $JeeMySensorsCmd->setTemplate('mobile', $cmd['templateMob']);
                $JeeMySensorsCmd->save();
           }
        }
    }

    // Supprime les commandes et ou infos par défaut
    public function removeCommande($idlogic) {
        foreach ($idlogic->getCmd() as $cmd) {
            $cmd->remove();
        }
        $idlogic->save();
    }

    // traite les messages envoyé par le gw - retour état
    public static function sendByGW($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload) {
        global $_api;
        $idlogic_node = 'Sensor-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic_etat = 'etat-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic = self::byLogicalId($idlogic_node, 'JeeMySensors');
        if (is_object($idlogic)) {
            foreach ($idlogic->getCmd() as $cmd) {
              if ($cmd->getLogicalId() === $idlogic_etat ) {
                $cmd->event($payload);
                $cmd->save();
              }
            }
            log::add('JeeMySensors', 'info',  '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Message envoyé :');
            log::add('JeeMySensors', 'info', '    \___ Node : ' . $id_node . " et sensor ID : " . $id_sensor);
            log::add('JeeMySensors', 'info', '        \___ Action : ' . $_api['C'][$command] . ' -> ' . $payload . ' pour ' . $_api['V'][$type][0]);
        }
    }
}

class JeeMySensorsCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        $eqLogic = $this->getEqLogic();
        if ($_options != null) {
            $value = explode("-", $this->getLogicalId());
            $id_gw_jeedom = $eqLogic->getConfiguration('id_node_gw');
            $id_node = $eqLogic->getConfiguration('id_node');
            $id_sensor = $eqLogic->getConfiguration('id_sensor');
            $command = '1';                                         // 1 -> SET
            $ack = '0';                                             // ack 0 -> inactif | 1 -> actif
            switch ($this->getSubType()) {
                case 'other':
                    switch ($value[0]) {
                        case 'on':
                            $type = '2';                            // 2 -> V_STATUS
                            $payload = '1';                         // 1 -> ON
                            break;
                        case 'off':
                            $type = '2';                            // 2 -> V_STATUS
                            $payload = '0';                         // 1 -> ON
                            break;
                        case 'up':
                            $type = '29';                           // 29 -> V_UP
                            $payload = '';                          //
                            break;
                        case 'down':
                            $type = '30';                           // 28 -> V_DOWN
                            $payload = '';                          //
                            break;;
                        case 'stop':
                            $type = '31';                           // 31 -> V_STOP
                            $payload = '';                          //
                            break;
                    }
                    break;
                case 'color':
                        $type = '40';                                // 40 -> V_RGB
                        $payload = str_replace('#', '', $_options['color']);
                    break;
                case 'slider':
                        $type = '3';                                // 3 -> V_PERCENTAGE
                        $payload = $_options['slider'];             //
                    break;
                case 'message':
                        $type = '47';                                // 3 -> V_TEXT
                        $payload = $_options['message'];             //
                    break;
            }

            // node-id ; child-sensor-id ; command ; ack ; type ; payload
            $actions = array (
                $id_node,                                       // node-id
                $id_sensor,                                     // child-sensor-id
                $command,                                       // command
                $ack,                                           // ack
                $type,                                          // type
                $payload                                        // payload
            );
            $action = implode(";", $actions);
            JeeMySensors::sendSocket($id_gw_jeedom, $action, 'send');
            JeeMySensors::sendByGW($id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
        }
        return;
    }

    /*     * **********************Getteur Setteur*************************** */
}
