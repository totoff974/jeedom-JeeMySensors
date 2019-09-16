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
global $listCmdJeeMySensors;
$listCmdJeeMySensors = array(
  'S_DOOR' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => true, 'generic_type' => 'OPENING', 'forceReturnLineAfter' => '1', 'templateDas' => 'door', 'templateMob' => 'door'),
  ),
  'S_MOTION' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => true, 'generic_type' => 'PRESENCE', 'forceReturnLineAfter' => '1', 'templateDas' => 'presence', 'templateMob' => 'presence'),
  ),
  'S_SMOKE' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => true, 'generic_type' => 'SMOKE', 'forceReturnLineAfter' => '1', 'templateDas' => 'alert', 'templateMob' => 'alert'),
  ),
  'S_BINARY' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => false, 'generic_type' => 'LIGHT_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => true,  'generic_type' => 'LIGHT_ON',    'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => true,  'generic_type' => 'LIGHT_OFF',   'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
  ),
  'S_DIMMER' => array(
    array('name' => 'Etat',        'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => false, 'generic_type' => 'LIGHT_STATE',    'forceReturnLineAfter' => '1', 'templateDas' => 'default', 'templateMob' => 'default'),
    array('name' => 'Intensité',   'logicalId' => 'dim',  'type' => 'action', 'subType' => 'slider',  'order' => 2, 'isVisible' => true,  'generic_type' => 'LIGHT_SLIDER',    'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'On',          'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',   'order' => 3, 'isVisible' => true,  'generic_type' => 'LIGHT_ON',        'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'Off',         'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',   'order' => 4, 'isVisible' => true,  'generic_type' => 'LIGHT_OFF',       'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
  ),
  'S_COVER' => array(
    array('name' => 'Etat',        'logicalId' => 'etat',   'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => false, 'generic_type' => 'FLAP_STATE',    'forceReturnLineAfter' => '1', 'templateDas' => 'shutter', 'templateMob' => 'shutter'),
    array('name' => 'Position',    'logicalId' => 'dim',    'type' => 'action', 'subType' => 'slider',  'order' => 2, 'isVisible' => true,  'generic_type' => 'FLAP_SLIDER',    'forceReturnLineAfter' => '1', 'templateDas' => 'shutter', 'templateMob' => 'shutter'),
    array('name' => 'Monter',      'logicalId' => 'up',     'type' => 'action', 'subType' => 'other',   'order' => 3, 'isVisible' => true,  'generic_type' => 'FLAP_UP',        'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
    array('name' => 'Descendre',   'logicalId' => 'down',   'type' => 'action', 'subType' => 'other',   'order' => 4, 'isVisible' => true,  'generic_type' => 'FLAP_DOWN',       'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
    array('name' => 'Stop',        'logicalId' => 'stop',   'type' => 'action', 'subType' => 'other',   'order' => 5, 'isVisible' => true,  'generic_type' => 'FLAP_STOP',       'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
  ),
  'S_TEMP' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'TEMPERATURE', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_HUM' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'HUMIDITY', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_BARO' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => true, 'generic_type' => 'PRESSURE', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_WIND' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'WIND_SPEED', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_RAIN' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'RAIN_CURRENT', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_UV' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'UV', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_WEIGHT' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_POWER' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'POWER', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_HEATER' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => true, 'generic_type' => 'HEATING_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'heat', 'templateMob' => 'heat'),
  ),
  'S_DISTANCE' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_LIGHT_LEVEL' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'BRIGHTNESS', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_ARDUINO_NODE' => array(

  ),
  'S_ARDUINO_REPEATER_NODE' => array(

  ),
  'S_LOCK' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => true, 'generic_type' => 'LOCK_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'lock', 'templateMob' => 'lock'),
  ),
  'S_IR' => array(

  ),
  'S_WATER' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'CONSUMPTION', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_AIR_QUALITY' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_CUSTOM' => array(

  ),
  'S_DUST' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_SCENE_CONTROLLER' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => false, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
    array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => true, 'generic_type' => 'GENERIC',    'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
    array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => true, 'generic_type' => 'GENERIC',   'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
  ),
  'S_RGB_LIGHT' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => false, 'generic_type' => 'LIGHT_COLOR', 'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => true, 'generic_type' => 'LIGHT_COLOR',    'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => true, 'generic_type' => 'LIGHT_COLOR',   'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
  ),
  'S_RGBW_LIGHT' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => false, 'generic_type' => 'LIGHT_COLOR', 'forceReturnLineAfter' => '1', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'On',   'logicalId' => 'on',   'type' => 'action', 'subType' => 'other',  'order' => 2, 'isVisible' => true, 'generic_type' => 'LIGHT_COLOR',    'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
    array('name' => 'Off',  'logicalId' => 'off',  'type' => 'action', 'subType' => 'other',  'order' => 3, 'isVisible' => true, 'generic_type' => 'LIGHT_COLOR',   'forceReturnLineAfter' => '0', 'templateDas' => 'light', 'templateMob' => 'light'),
  ),
  'S_COLOR_SENSOR' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => true, 'generic_type' => 'LIGHT_COLOR', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_HVAC' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => true, 'generic_type' => 'HEATING_STATE', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_MULTIMETER' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_SPRINKLER' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_WATER_LEAK' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'binary', 'order' => 1, 'isVisible' => true, 'generic_type' => 'FLOOD', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_SOUND' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_VIBRATION' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_MOISTURE' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_INFO' => array(
    array('name' => 'Etat',   'logicalId' => 'etat', 'type' => 'info',   'subType' => 'string',   'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC_INFO',     'forceReturnLineAfter' => '1', 'templateDas' => 'default', 'templateMob' => 'default'),
    array('name' => 'Texte',  'logicalId' => 'msg',  'type' => 'action', 'subType' => 'message',  'order' => 2, 'isVisible' => true, 'generic_type' => 'GENERIC_ACTION',   'forceReturnLineAfter' => '0', 'templateDas' => 'default', 'templateMob' => 'default'),
  ),
  'S_GAS' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_GPS' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
  'S_WATER_QUALITY' => array(
    array('name' => 'Etat', 'logicalId' => 'etat', 'type' => 'info',   'subType' => 'numeric', 'order' => 1, 'isVisible' => true, 'generic_type' => 'GENERIC', 'forceReturnLineAfter' => '1', 'templateDas' => 'line', 'templateMob' => 'line'),
  ),
);
?>
