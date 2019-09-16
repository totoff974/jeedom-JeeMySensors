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

require_once __DIR__  . '/../../../../core/php/core.inc.php';
/*
 * Non obligatoire mais peut être utilisé si vous voulez charger en même temps que votre
 * plugin des librairies externes (ne pas oublier d'adapter plugin_info/info.xml).
 *
 *
 */
if (!jeedom::apiAccess(init('apikey'), 'JeeMySensors')) {
    echo __('Vous n\'etes pas autorisé à effectuer cette action', __FILE__);
    die();
}

if (isset($_GET['test'])) {
    echo 'OK';
    die();
}

$result = json_decode(file_get_contents("php://input"), true);
if (!is_array($result)) {
    die();
}

if (isset($result['erreur'])) {
    $datas = $result['erreur'];
    $id_gw_jeedom = $datas['gw'];
    $msg_erreur = $datas['msg_erreur'];
    // plus de 10 tentatives on setActive = 0
    JeeMySensors::gestion_Erreur($id_gw_jeedom, $msg_erreur);
}

if (isset($result['message'])) {
    $datas = $result['message'];
    if ($datas['typeOfco'] === 'LAN') { $typeOfco = '  LAN  - ';}
    if ($datas['typeOfco'] === 'SERIAL') { $typeOfco = 'SERIAL - ';}
    $id_gw_jeedom = $datas['gw'];
    $id_node = $datas['node-id'];
    $id_sensor = $datas['child-sensor-id'];
    $command = $datas['command'];
    $ack = $datas['ack'];
    $type = $datas['type'];
    $payload = str_replace("\n", "", $datas['payload']);
    log::add('JeeMySensors', 'info', '[moniteur] ' . $typeOfco . $id_gw_jeedom . ' --> ' . $id_node . ';' . $id_sensor . ';' . $command . ';' . $ack . ';' . $type . ';' . $payload);

    switch ($command) {
      // presentation
      case '0':
          JeeMySensors::mode_Presentation('quiet', $datas['typeOfco'], $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
          break;
      // set
      case '1':
          JeeMySensors::mode_Set('quiet', $datas['typeOfco'], $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
          break;
      // req
      case '2':
          JeeMySensors::mode_Req('quiet', $datas['typeOfco'], $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
          break;
      // internal
      case '3':
          JeeMySensors::mode_Internal('quiet', $datas['typeOfco'], $id_gw_jeedom, $id_node, $id_sensor, $command, $ack, $type, $payload);
          break;
      // stream
      case '4':

          break;
    }
}
