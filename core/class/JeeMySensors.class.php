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

class JeeMySensors extends eqLogic {

    /**
     * @var array MySensors dictionary
     */
    static public $_dictionary = array(
        // Capabilities
        'CAPAB' => array(
            0 => array('CAP' => 'MY_CAP_RESET', 'R' => 'Actif', 'N' => 'Inactif'),
            1 => array('CAP' => 'MY_CAP_RADIO', 'N' => 'nRF24 / nRF5', 'R' => 'RFM69 (ancien)', 'P' => 'RFM69 (nouveau)', 'L' => 'RFM95', 'S' => 'RS485', '-' => 'Aucun'),
            2 => array('CAP' => 'MY_CAP_OTA_FW', 'O' => 'Actif', 'N' => 'Inactif'),
            3 => array('CAP' => 'MY_CAP_TYPE', 'G' => 'Passerrelle', 'R' => 'Répétiteur', 'P' => 'Passif', 'N' => 'Noeud'),
            4 => array('CAP' => 'MY_CAP_ARCH', 'S' => 'SAMD', 'N' => 'nRF5', 'E' => 'ESP8266', 'A' => 'AVR', 'F' => 'STM32F1', 'T' => 'TEENSY', 'L' => 'Linux', '-' => 'Inconnu'),
            5 => array('CAP' => 'MY_CAP_SIGN', 'A' => 'ATSHA204', 'S' => 'Logiciel', '-' => 'Pas de signature'),
            6 => array('CAP' => 'MY_CAP_RXBUF', 'Q' => 'Actif', '-' => 'Inactif'),
            7 => array('CAP' => 'MY_CAP_ENCR', 'X' => 'Actif', '-' => 'Inactif'),
        ),
        // Command
        'C' => array(
            0 => 'presentation',     // Envoyés par un nœud lorsqu'ils présentent des capteurs attachés. Cela se fait généralement dans la fonction presentation () qui s'exécute au démarrage.
            1 => 'set',              // Ce message est envoyé depuis ou vers un capteur lorsqu'une valeur de capteur doit être mise à jour.
            2 => 'req',              // Demande une valeur variable (généralement d'un actionneur destiné au contrôleur).
            3 => 'interne',          // Ceci est un message interne spécial. Voir le tableau ci-dessous pour les détails
            4 => 'stream',           // Utilisé pour les mises à jour de micrologiciels OTA
        ),
        // Presentation
        'S' =>  array(
            0 => array('S_DOOR', 'Capteur de porte et de fenêtre', 'V_TRIPPED,V_ARMED'),
            1 => array('S_MOTION', 'Capteur de mouvement', 'V_TRIPPED,V_ARMED'),
            2 => array('S_SMOKE', 'Détecteur de fumée', 'V_TRIPPED,V_ARMED'),
            3 => array('S_BINARY', 'Périphérique binaire (on / off)', 'V_STATUS,V_WATT'),
            4 => array('S_DIMMER', 'Dispositif dimmable', 'V_STATUS,V_PERCENTAGE,V_WATT'),
            5 => array('S_COVER', 'Fenêtre ou store', 'V_UP,V_DOWN,V_STOP,V_PERCENTAGE'),
            6 => array('S_TEMP', 'Capteur de température', 'V_TEMP,V_ID'),
            7 => array('S_HUM', 'Capteur d\'humidité', 'V_HUM'),
            8 => array('S_BARO', 'Capteur de pression (baromètre)', 'V_PRESSURE,V_FORECAST'),
            9 => array('S_WIND', 'Capteur de vent', 'V_WIND,V_GUST,V_DIRECTION'),
            10 => array('S_RAIN', 'Capteur de pluie', 'V_RAIN,V_RAINRATE'),
            11 => array('S_UV', 'Capteur UV', 'V_UV'),
            12 => array('S_WEIGHT', 'Capteur de poids', 'V_WEIGHT,V_IMPEDANCE'),
            13 => array('S_POWER', 'Appareil de mesure de la puissance', 'V_WATT,V_KWH,V_VAR,V_VA,V_POWER_FACTOR'),
            14 => array('S_HEATER', 'Dispositif de chauffage', 'V_HVAC_SETPOINT_HEAT,V_HVAC_FLOW_STATE,V_TEMP,V_STATUS'),
            15 => array('S_DISTANCE', 'Capteur de distance', 'V_DISTANCE,V_UNIT_PREFIX'),
            16 => array('S_LIGHT_LEVEL', 'Capteur de lumière', 'V_LIGHT_LEVEL,V_LEVEL'),
            17 => array('S_ARDUINO_NODE', 'Périphérique Arduino',),
            18 => array('S_ARDUINO_REPEATER_NODE', 'Périphérique de nœud répétitif Arduino',),
            19 => array('S_LOCK', 'Dispositif de verrouillage', 'V_LOCK_STATUS'),
            20 => array('S_IR', 'Ir émetteur / récepteur', 'V_IR_SEND,V_IR_RECEIVE,V_IR_RECORD'),
            21 => array('S_WATER', 'Compteur d\'eau', 'V_FLOW,V_VOLUME'),
            22 => array('S_AIR_QUALITY', 'Capteur de qualité de l\'air', 'V_LEVEL,V_UNIT_PREFIX'),
            23 => array('S_CUSTOM', 'Capteur personnalisé',),
            24 => array('S_DUST', 'Capteur de niveau de poussière', 'V_LEVEL,V_UNIT_PREFIX'),
            25 => array('S_SCENE_CONTROLLER', 'Dispositif de contrôle de scène', 'V_SCENE_ON,V_SCENE_OFF'),
            26 => array('S_RGB_LIGHT', 'Lumière RVB', 'V_RGB,V_WATT'),
            27 => array('S_RGBW_LIGHT', 'Lumière RGBW', 'V_RGBW,V_WATT'),
            28 => array('S_COLOR_SENSOR', 'Capteur de couleur', 'V_RGB'),
            29 => array('S_HVAC', 'Thermostat / dispositif de CVC', 'V_STATUS,V_TEMP,V_HVAC_SETPOINT_HEAT,V_HVAC_SETPOINT_COOL,V_HVAC_FLOW_STATE,V_HVAC_FLOW_MODE,V_HVAC_SPEED'),
            30 => array('S_MULTIMETER', 'Appareil multimètre', 'V_VOLTAGE,V_CURRENT,V_IMPEDANCE'),
            31 => array('S_SPRINKLER', 'Dispositif d\'arrosage', 'V_STATUS,V_TRIPPED'),
            32 => array('S_WATER_LEAK', 'Capteur de fuite d\'eau', 'V_TRIPPED,V_ARMED'),
            33 => array('S_SOUND', 'Capteur sonore', 'V_LEVEL,V_TRIPPED,V_ARMED'),
            34 => array('S_VIBRATION', 'Capteur de vibrations', 'V_LEVEL,V_TRIPPED,V_ARMED'),
            35 => array('S_MOISTURE', 'Capteur d\'humidité', 'V_LEVEL,V_TRIPPED,V_ARMED'),
            36 => array('S_INFO', 'Appareil texte LCD', 'V_TEXT'),
            37 => array('S_GAS', 'Compteur à gaz', 'V_FLOW,V_VOLUME'),
            38 => array('S_GPS', 'Capteur GPS', 'V_POSITION'),
            39 => array('S_WATER_QUALITY', 'Capteur de qualité de l\'eau', 'V_TEMP,V_PH,V_ORP,V_EC,V_STATUS'),
        ),
        // SET / REQ
        'V' =>  array(
            0 => array('V_TEMP', 'Température', 'S_TEMP,S_HEATER,S_HVAC,S_WATER_QUALITY'),
            1 => array('V_HUM', 'Humidité', 'S_HUM'),
            2 => array('V_STATUS', 'Statut binaire. 0 = désactivé 1 = activé', 'S_BINARY,S_DIMMER,S_SPRINKLER,S_HVAC,S_HEATER,S_WATER_QUALITY'),
            3 => array('V_PERCENTAGE', 'Valeur en pourcentage 0-100 (%)', 'S_DIMMER,S_COVER'),
            4 => array('V_PRESSURE', 'Pression atmosphérique', 'S_BARO'),
            5 => array('V_FORECAST', '"stable", "ensoleillé", "nuageux", "instable", "orage" ou "inconnu"', 'S_BARO'),
            6 => array('V_RAIN', 'Quantité de pluie', 'S_RAIN'),
            7 => array('V_RAINRATE', 'Taux de pluie', 'S_RAIN'),
            8 => array('V_WIND', 'Vitesse du vent', 'S_WIND'),
            9 => array('V_GUST', 'Rafale', 'S_WIND'),
            10 => array('V_DIRECTION', 'Direction du vent 0-360 (degrés)', 'S_WIND'),
            11 => array('V_UV   ', 'Niveau de lumière UV', 'S_UV'),
            12 => array('V_WEIGHT', 'Poids', 'S_WEIGHT'),
            13 => array('V_DISTANCE', 'Distance', 'S_DISTANCE'),
            14 => array('V_IMPEDANCE', 'Valeur d\'impédance', 'S_MULTIMETER,S_WEIGHT'),
            15 => array('V_ARMED', '1 = armé, 0 = désarmé', 'S_DOOR,S_MOTION,S_SMOKE,S_SPRINKLER,S_WATER_LEAK,S_SOUND,S_VIBRATION,S_MOISTURE'),
            16 => array('V_TRIPPED', '1 = déclenché, 0 = enclenché', 'S_DOOR,S_MOTION,S_SMOKE,S_SPRINKLER,S_WATER_LEAK,S_SOUND,S_VIBRATION,S_MOISTURE'),
            17 => array('V_WATT', 'Valeur en watts pour les compteurs d\'énergie', 'S_POWER,S_BINARY,S_DIMMER,S_RGB_LIGHT,S_RGBW_LIGHT'),
            18 => array('V_KWH', 'Valeur en watts pour les compteurs d\'énergie', 'S_POWER'),
            19 => array('V_SCENE_ON', 'Allumer une scène', 'S_SCENE_CONTROLLER'),
            20 => array('V_SCENE_OFF', 'Eteindre une scène', 'S_SCENE_CONTROLLER'),
            21 => array('V_HVAC_FLOW_STATE', '"Off", "HeatOn", "CoolOn" ou "AutoChangeOver"', 'S_HVAC,S_HEATER'),
            22 => array('V_HVAC_SPEED', 'Vitesse du ventilateur CVC / chauffage ("Min", "Normal", "Max", "Auto")', 'S_HVAC,S_HEATER'),
            23 => array('V_LIGHT_LEVEL', 'Niveau de lumière non calibré. 0-100%. Utilisez V_LEVEL pour le niveau d\'éclairage en lux.', 'S_LIGHT_LEVEL'),
            24 => array('V_VAR1', 'Valeur personnalisée ', 'all'),
            25 => array('V_VAR2', 'Valeur personnalisée ', 'all'),
            26 => array('V_VAR3', 'Valeur personnalisée ', 'all'),
            27 => array('V_VAR4', 'Valeur personnalisée ', 'all'),
            28 => array('V_VAR5', 'Valeur personnalisée ', 'all'),
            29 => array('V_UP', 'Fenêtre. Up.', 'S_COVER'),
            30 => array('V_DOWN', 'Fenêtre. Vers le bas.', 'S_COVER'),
            31 => array('V_STOP', 'Fenêtre. Arrêtez.', 'S_COVER'),
            32 => array('V_IR_SEND', 'Envoyer une commande IR', 'S_IR'),
            33 => array('V_IR_RECEIVE', 'Ce message contient une commande IR reçue', 'S_IR'),
            34 => array('V_FLOW', 'Débit d\'eau / gaz (en mètre)', 'S_WATER,S_GAS'),
            35 => array('V_VOLUME', 'Volume eau / gaz', 'S_WATER,S_GAS'),
            36 => array('V_LOCK_STATUS', '1 = verrouillé, 0 = déverrouillé', 'S_LOCK'),
            37 => array('V_LEVEL', 'Utilisé pour l\'envoi de niveau-valeur', 'S_DUST,S_AIR_QUALITY,S_SOUND,S_VIBRATION,S_LIGHT_LEVEL'),
            38 => array('V_VOLTAGE', 'Niveau de tension', 'S_MULTIMETER'),
            39 => array('V_CURRENT', 'Niveau de courant', 'S_MULTIMETER'),
            40 => array('V_RGB', 'Valeur RVB transmise sous forme de chaîne hexadécimale ASCII (Ie "ff0000" pour le rouge)', 'S_RGB_LIGHT,S_COLOR_SENSOR'),
            41 => array('V_RGBW', 'Valeur RGBW transmise sous forme de chaîne hexadécimale ASCII (c\'est-à-dire "ff0000ff" pour le rouge et le blanc)', 'S_RGBW_LIGHT'),
            42 => array('V_ID', 'Identifiant unique facultatif du capteur (par exemple, identifiants OneWire DS1820b)', 'S_TEMP'),
            43 => array('V_UNIT_PREFIX', 'Permet aux capteurs d\'envoyer une chaîne représentant le préfixe de l\'unité à afficher dans l\'interface graphique. Ce n\'est pas analysé par le contrôleur! Par exemple cm, m, km, inch.', 'S_DISTANCE,S_DUST,S_AIR_QUALITY'),
            44 => array('V_HVAC_SETPOINT_COOL', 'Point de consigne froid CVC', 'S_HVAC'),
            45 => array('V_HVAC_SETPOINT_HEAT', 'Point de consigne CVC / chauffage', 'S_HVAC,S_HEATER'),
            46 => array('V_HVAC_FLOW_MODE', 'Mode de flux pour CVC ("Auto", "ContinuousOn", "PeriodicOn")', 'S_HVAC'),
            47 => array('V_TEXT', 'Message texte à afficher sur l\'écran LCD ou le dispositif de commande', 'S_INFO'),
            48 => array('V_CUSTOM', 'Messages personnalisés utilisés pour les commandes spécifiques aux contrôleurs / nœuds, en utilisant de préférence le type de périphérique S_CUSTOM.', 'S_CUSTOM'),
            49 => array('V_POSITION', 'Position GPS et altitude. Charge utile: latitude, longitude et altitude (m). Par exemple: "55,722526; 13,017972; 18"', 'S_GPS'),
            50 => array('V_IR_RECORD', 'Enregistrer les codes IR S_IR pour la lecture', 'S_IR'),
            51 => array('V_PH', 'Eau PH', 'S_WATER_QUALITY'),
            52 => array('V_ORP', 'Eau ORP: potentiel rédox en mV', 'S_WATER_QUALITY'),
            53 => array('V_EC', 'Conductivité électrique de l\'eau μS / cm (microSiemens / cm)', 'S_WATER_QUALITY'),
            54 => array('V_VAR', 'Puissance réactive: Volt-Ampère Réactif (var)', 'S_POWER'),
            55 => array('V_VA', 'Puissance apparente: voltampère (VA)', 'S_POWER'),
            56 => array('V_POWER_FACTOR', 'Ratio du pouvoir réel sur le pouvoir apparent: valeur en virgule flottante comprise dans l\'intervalle [-1, .., 1]', 'S_POWER'),
        ),
        // Internal
        'I' => array(
            0 => array('I_BATTERY_LEVEL', 'Utilisez-le pour signaler le niveau de la batterie (en pourcentage de 0 à 100)'),
            1 => array('I_TIME', 'Les capteurs peuvent demander l\'heure actuelle au contrôleur à l\'aide de ce message. Le temps sera rapporté comme les secondes depuis 1970'),
            2 => array('I_VERSION', 'Utilisé pour demander la version de la passerelle au contrôleur.'),
            3 => array('I_ID_REQUEST', 'Utilisez-le pour demander un identifiant de noeud unique au contrôleur.'),
            4 => array('I_ID_RESPONSE', 'Réponse Id retour au noeud. La charge contient l\'id du nœud.'),
            5 => array('I_INCLUSION_MODE', 'Démarrer / arrêter le mode d\’inclusion du contrôleur (1 = démarrer, 0 = arrêter).'),
            6 => array('I_CONFIG', 'Demande de configuration du noeud. Répondez avec (M) etric ou (I) au dos du capteur.'),
            7 => array('I_FIND_PARENT', 'Lorsqu\'un capteur démarre, il envoie une requête de recherche à tous les nœuds voisins. Ils répondent avec un I_FIND_PARENT_RESPONSE.'),
            8 => array('I_FIND_PARENT_RESPONSE', 'Répondre au type de message à la demande I_FIND_PARENT.'),
            9 => array('I_LOG_MESSAGE', 'Envoyé par la passerelle au contrôleur pour enregistrer un message dans le journal de suivi'),
            10 => array('I_CHILDREN', 'Un message qui peut être utilisé pour transférer des capteurs enfants (à partir de la table de routage EEPROM) d\'un noeud répétitif.'),
            11 => array('I_SKETCH_NAME', 'Nom d\'esquisse facultatif pouvant être utilisé pour identifier le capteur dans l\'interface graphique du contrôleur'),
            12 => array('I_SKETCH_VERSION', 'Version d\'esquisse facultative pouvant être signalée pour garder une trace de la version du capteur dans l\'interface graphique du contrôleur.'),
            13 => array('I_REBOOT', 'Utilisé par les mises à jour du micrologiciel de l\'OTA. Demande au nœud de redémarrer.'),
            14 => array('I_GATEWAY_READY', 'Envoyer par passerelle au contrôleur lorsque le démarrage est terminé.'),
            15 => array('I_SIGNING_PRESENTATION', 'Fournit les préférences liées à la signature (le premier octet est la version de préférence).'),
            16 => array('I_NONCE_REQUEST', 'Utilisé entre les capteurs lors de la demande de nonce.'),
            17 => array('I_NONCE_RESPONSE', 'Utilisé entre les capteurs pour la réponse nonce.'),
            18 => array('I_HEARTBEAT_REQUEST', 'Demande de pulsation'),
            19 => array('I_PRESENTATION', 'Message de présentation'),
            20 => array('I_DISCOVER_REQUEST', 'Demande de découverte'),
            21 => array('I_DISCOVER_RESPONSE', 'Découvrez la réponse'),
            22 => array('I_HEARTBEAT_RESPONSE', 'Réponse de battement de coeur'),
            23 => array('I_LOCKED', 'Le nœud est verrouillé (raison dans string-payload)'),
            24 => array('I_PING', 'Ping envoyé au nœud, compteur de sauts incrémentiels de charge utile'),
            25 => array('I_PONG', 'En réponse à ping, renvoyé à l\'expéditeur, compteur de sauts incrémentiels de charge utile'),
            26 => array('I_REGISTRATION_REQUEST', 'Enregistrez votre demande à GW'),
            27 => array('I_REGISTRATION_RESPONSE', 'Enregistrer la réponse de GW'),
            28 => array('I_DEBUG', 'Message de débogage'),
        )
    );

    /**
     * @var array Cmd auto creation when discover
     */
    static public $_cmdDefaults = array(
        'S_DOOR' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'OPENING', 'forceReturnLineAfter' => '1', 'templateDas' => 'door', 'templateMob' => 'door'),
        ),
        'S_MOTION' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'PRESENCE', 'forceReturnLineAfter' => '1', 'templateDas' => 'presence', 'templateMob' => 'presence'),
        ),
        'S_SMOKE' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'SMOKE', 'forceReturnLineAfter' => '1', 'templateDas' => 'alert', 'templateMob' => 'alert'),
        ),
        'S_BINARY' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 0, 'generic_type' => 'LIGHT_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => 1,  'generic_type' => 'LIGHT_ON',    'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => 1,  'generic_type' => 'LIGHT_OFF',   'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
        ),
        'S_DIMMER' => array(
            array('name' => 'Etat',        'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 0, 'generic_type' => 'LIGHT_STATE',    'forceReturnLineAfter' => '1', 'templateDas' => 'default', 'templateMob' => 'default'),
            array('name' => 'Intensité',   'logicalId' => 'dim',  'type' => 'action', 'subType' => 'slider',  'order' => 2, 'isVisible' => 1,  'generic_type' => 'LIGHT_SLIDER',    'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'On',          'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',   'order' => 3, 'isVisible' => 1,  'generic_type' => 'LIGHT_ON',        'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'Off',         'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',   'order' => 4, 'isVisible' => 1,  'generic_type' => 'LIGHT_OFF',       'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
        ),
        'S_COVER' => array(
            array('name' => 'Etat',        'logicalId' => 'etat',   'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 0, 'generic_type' => 'FLAP_STATE',    'forceReturnLineAfter' => '1', 'templateDas' => 'shutter', 'templateMob' => 'shutter'),
            array('name' => 'Position',    'logicalId' => 'dim',    'type' => 'action', 'subType' => 'slider',  'order' => 2, 'isVisible' => 1,  'generic_type' => 'FLAP_SLIDER',    'forceReturnLineAfter' => '1', 'templateDas' => 'shutter', 'templateMob' => 'shutter'),
            array('name' => 'Monter',      'logicalId' => 'up',     'type' => 'action', 'subType' => 'other',   'order' => 3, 'isVisible' => 1,  'generic_type' => 'FLAP_UP',        'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
            array('name' => 'Descendre',   'logicalId' => 'down',   'type' => 'action', 'subType' => 'other',   'order' => 4, 'isVisible' => 1,  'generic_type' => 'FLAP_DOWN',       'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
            array('name' => 'Stop',        'logicalId' => 'stop',   'type' => 'action', 'subType' => 'other',   'order' => 5, 'isVisible' => 1,  'generic_type' => 'FLAP_STOP',       'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
        ),
        'S_TEMP' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'TEMPERATURE', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_HUM' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'HUMIDITY', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_BARO' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'PRESSURE', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_WIND' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'WIND_SPEED', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_RAIN' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'RAIN_CURRENT', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_UV' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'UV', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_WEIGHT' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_POWER' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'POWER', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_HEATER' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'HEATING_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'heat', 'templateMob' => 'heat'),
        ),
        'S_DISTANCE' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_LIGHT_LEVEL' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'BRIGHTNESS', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_ARDUINO_NODE' => array(

        ),
        'S_ARDUINO_REPEATER_NODE' => array(

        ),
        'S_LOCK' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'LOCK_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'lock', 'templateMob' => 'lock'),
        ),
        'S_IR' => array(

        ),
        'S_WATER' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'CONSUMPTION', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_AIR_QUALITY' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_CUSTOM' => array(

        ),
        'S_DUST' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_SCENE_CONTROLLER' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 0, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
            array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => 1, 'generic_type' => 'GENERIC',    'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
            array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => 1, 'generic_type' => 'GENERIC',   'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
        ),
        'S_RGB_LIGHT' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 0, 'generic_type' => 'LIGHT_COLOR', 'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => 1, 'generic_type' => 'LIGHT_COLOR',    'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => 1, 'generic_type' => 'LIGHT_COLOR',   'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
        ),
        'S_RGBW_LIGHT' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 0, 'generic_type' => 'LIGHT_COLOR', 'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => 1, 'generic_type' => 'LIGHT_COLOR',    'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
            array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => 1, 'generic_type' => 'LIGHT_COLOR',   'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
        ),
        'S_COLOR_SENSOR' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'LIGHT_COLOR', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_HVAC' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'HEATING_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_MULTIMETER' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_SPRINKLER' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_WATER_LEAK' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'FLOOD', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_SOUND' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_VIBRATION' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_MOISTURE' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_INFO' => array(
            array('name' => 'Etat',   'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string',   'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC_INFO',     'forceReturnLineAfter' => '1', 'templateDas' => 'default', 'templateMob' => 'default'),
            array('name' => 'Texte',  'logicalId' => 'msg',  'type' => 'action', 'subType' => 'message',  'order' => 2, 'isVisible' => 1, 'generic_type' => 'GENERIC_ACTION',   'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
        ),
        'S_GAS' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_GPS' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
        'S_WATER_QUALITY' => array(
            array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => 1, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
        ),
    );

    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */
    public static function dependancy_info() {
        $return = array();
        $return['progress_file'] = jeedom::getTmpFolder('JeeMySensors') . '/dependance';
        if (exec(system::getCmdSudo() . system::get('cmd_check') . '-E "python\-serial|python\-request|python\-pyudev" | wc -l') >= 3) {
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
        $idlogic_sensor = 'Sensor-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic_etat = 'etat-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic = self::byLogicalId($idlogic_sensor, 'JeeMySensors');
        if (is_object($idlogic)) {
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . '] |--> Message reçu sur le Gateway');
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . ']     \___ Node : ' . $id_node . " et sensor ID : " . $id_sensor);
            log::add('JeeMySensors', 'info', '[' . str_pad($id_gw_jeedom, 5, " ", STR_PAD_BOTH) . ']         \___ Action : ' . static::$_dictionary['C'][$command] . ' -> ' . $payload . ' pour ' . static::$_dictionary['V'][$type][0]);
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
                $type_S_nom = static::$_dictionary['S'][$type][0];
                $type_nom = static::$_dictionary['S'][$type][1];
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
        $idlogic_sensor = 'Sensor-' . $id_node . '-' . $id_sensor . ':' . $id_gw_jeedom;
        $idlogic = self::byLogicalId($idlogic_sensor, 'JeeMySensors');
        $type_S_nom = static::$_dictionary['S'][$type][0];
        $type_nom = static::$_dictionary['S'][$type][1];
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
        foreach (static::$_cmdDefaults[$type_S_nom] as $cmd) {
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
            log::add('JeeMySensors', 'info', '        \___ Action : ' . static::$_dictionary['C'][$command] . ' -> ' . $payload . ' pour ' . static::$_dictionary['V'][$type][0]);
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
            $ack = '1';                                             // ack 0 -> inactif | 1 -> actif
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
                            break;
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

            // Override by defined configuration
            if ($this->getType() === 'action') {

                $cmdCommand = $this->getConfiguration('cmdCommand');
                $request = $this->getConfiguration('request');
                $cmdType = $this->getConfiguration('cmdType');

                // Change $command if not empty
                if (!empty($cmdCommand)) {
                    $command = $cmdCommand;
                }

                // Request
                if ($request !== '') {
                    if ($request === '#slider#') {
                        $payload = $_options['slider'];
                    } else if ($request === '#color#') {
                        $payload = $_options['color'];
                    } else if ($request === '#message#') {
                        $payload = $_options['message'];
                    } else {
                        $payload = $request;
                    }
                }

                // Type
                if (!empty($cmdType)) {
                    $type = $cmdType;
                }
            }

            /**
             * TODO
             * 1. AJouter 3 champs sur l'interface de configuration des commandes d'un noeud
             *      * Type de capteur:  Par défaut à "1 - Paramétrage". Utilité : permettrait de faire autre chose depuis le noeud racine éventuellement.
             *                          Dans 99% des cas, c'est la valeur 1 que l'on utilisera.
             *                          A passer dans la variable $command
             *      * Valeur: Choix de la valeur à envoyer, à passer dans la variable $payload ensuite.
             *      * Type de donnée: Le type de donnée à envoyer. A passer dans la variable $type
             *                        Liste déroulante issue de JeeMySensors.api.php::$_api['V'] (index 0 dans chaque entrée avec suffixe '$key - ')
             *
             * 2. S'assurer que ces 3 champs sont enregistrés sur la commande. Comme sur le plugin MySensor, on peut les ajouter dans la configuration de la commande:
             *    config: cmdCommand, payload, cmdType = $type
             *
             * 3. Dans cette méthode (execute), s'assurer que ses 3 valeurs sont utilisées.
             *    On peut éventuellement conserver la compatibilité avec le switch/case du dessus en ajoutant le traitement des données dynamique après
             *    et seulement si le payload n'est pas vide.
             *    Quid compatibilité payload avec données dynamique type '#slider#' ou '#color#' ?
             *
             * 4. Lors de la création automatique des commandes (inclusion du noeud), configurer automatiquement le cmdType, cmdCommand et peut être payload
             *
             * 5. Vérifier que les actions s'affichent bien sur le dashboard à l'affichage.
             *
             * (Bonus) 6. Changer l'organisation d'affichage des noeuds sur la page du plugin : 1 ligne par noeud qui liste tous les sensors.
             */

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
