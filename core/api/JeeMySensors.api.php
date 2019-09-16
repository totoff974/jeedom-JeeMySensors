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

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

global $_api;
$_api =
    array(
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
?>
