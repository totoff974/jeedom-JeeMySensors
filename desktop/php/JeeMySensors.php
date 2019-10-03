<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('JeeMySensors');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
    <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
    <div class="eqLogicThumbnailContainer">
        <div class="cursor eqLogicAction logoPrimary" data-action="add">
            <i class="fas fa-plus-circle"></i>
            <br/>
            <span>{{Ajouter une Gateway}}</span>
        </div>
        <div class="cursor eqLogicAction logoPrimary" data-action="gotoPluginConf">
            <i class="fas fa-wrench"></i>
            <br/>
            <span>{{Configuration}}</span>
        </div>
    </div>
    <legend><i class="fas fa-table"></i> {{Mes Equipements}}</legend>
    <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
    <?php
    foreach ($eqLogics as $eqLogic) {
        if ($eqLogic->getConfiguration('role') === 'Gateway') {
            echo '<div class="eqLogicThumbnailContainer">';
            $opacity_gw = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
            echo '<div class="eqLogicDisplayCard cursor '.$opacity_gw.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
            if (file_exists('plugins/JeeMySensors/plugin_info/JeeMySensors_GW.png')) {
              echo '<img src="plugins/JeeMySensors/plugin_info/JeeMySensors_GW.png"/>';
            }
            else {
              echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
            }
            echo '<br>';
            echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
            echo '</div>';
            foreach ($eqLogics as $eqLogic_node) {
                if ($eqLogic_node->getConfiguration('id_node_gw') === $eqLogic->getId()) {
                    // if (strpos($eqLogic_node->getLogicalId(), 'Node') === 0) {
                        $opacity = ($eqLogic_node->getIsEnable()) ? '' : 'disableCard';
                        echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic_node->getId() . '">';
                        if (file_exists('plugins/JeeMySensors/plugin_info/JeeMySensors_' . $eqLogic_node->getConfiguration('id_role') . '.png')) {
                          echo '<img src="plugins/JeeMySensors/plugin_info/JeeMySensors_' . $eqLogic_node->getConfiguration('id_role') . '.png"/>';
                        }
                        else {
                          echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                        }
                        echo '<br>';
                        echo '<span class="name">' . $eqLogic_node->getHumanName(true, true) . '</span>';
                        echo '</div>';
                        foreach ($eqLogic_node as $eqLogic_sensor) {
                            if ($eqLogic_sensor->getConfiguration('id_node_gw') === $eqLogic->getId()) {
                                if (strpos($eqLogic_sensor->getLogicalId(), 'Sensor-'.$eqLogic_node->getConfiguration('id_node')) === 0) {
                                    $opacity = ($eqLogic_sensor->getIsEnable()) ? '' : 'disableCard';
                                    echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic_sensor->getId() . '">';
                                    if (file_exists('plugins/JeeMySensors/plugin_info/JeeMySensors_' . $eqLogic_sensor->getConfiguration('id_role') . '.png')) {
                                      echo '<img src="plugins/JeeMySensors/plugin_info/JeeMySensors_' . $eqLogic_sensor->getConfiguration('id_role') . '.png"/>';
                                    }
                                    else {
                                      echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                                    }
                                    echo '<br>';
                                    echo '<span class="name">' . $eqLogic_sensor->getHumanName(true, true) . '</span>';
                                    echo '</div>';
                                }
                            }
                        }
                    // }
                }
            }
            echo '</div>';
        }
    }
    ?>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
            </span>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br/>
                <div class="form-group">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group col-sm-8">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                                <div class="col-sm-3">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement }}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                                <div class="col-sm-3">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php
                                        foreach (jeeObject::all() as $object) {
                                            echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                                <div class="col-sm-9">
                                    <?php
                                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-9">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                                </div>
                            </div>
                            <br/>
                            <div id="isGateway">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" >{{Type du Gateway}}</label>
                                    <div class="col-sm-3">
                                        <select id="type_gw_select" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="type_gw">
                                            <option value="serial">{{Serial Gateway}}</option>
                                            <option value="lan">{{LAN Gateway}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="type_gw_serial" class="form-group">
                                    <!-- SERIAL -->
                                    <label id="type_gw_serial" class="col-sm-3 control-label" >{{Port du Gateway}}</label>
                                    <div class="col-sm-3">
                                        <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="port_gw">
                                            <option value="none">{{Aucun}}</option>
                                            <?php
                                            foreach (jeedom::getUsbMapping() as $name => $value) {
                                                echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
                                            }
                                            foreach (ls('/dev/', 'tty*') as $value) {
                                                echo '<option value="/dev/' . $value . '">/dev/' . $value . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div id="type_gw_lan" class="form-group">
                                    <!-- LAN -->
                                    <label id="type_gw_lan" class="col-sm-3 control-label" >{{IP:PORT du Gateway}}</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ip_gw" placeholder="192.168.XXX.XXX:5003"/>
                                    </div>
                                </div>
                                <br/>
                                <br/>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Connexion Timeout}}</label>
                                        <div class="col-sm-3">
                                            <input type="number" min="5" style="width:100%" class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="timeout"/>
                                        </div>
                                </div>
                            </div>
                            <div id="isSensor">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Id Jeedom Gateway}}</label>
                                    <div class="col-sm-3">
                                        <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="id_node_gw" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Id Sensor}}</label>
                                        <div class="col-sm-3">
                                            <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="id_sensor" />
                                        </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Rôle}}</label>
                                <div class="col-sm-3">
                                    <input type="hidden" class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="id_role"/>
                                    <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="role"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Id Node}}</label>
                                <div class="col-sm-3">
                                    <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="id_node" />
                                </div>
                            </div>
                            <div id="isNodeAndGateway">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Version}}</label>
                                        <div class="col-sm-3">
                                            <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="version" />
                                        </div>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="type_co" />
                                </div>
                            </div>
                            <div id="isNode">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Sketch Version}}</label>
                                        <div class="col-sm-3">
                                            <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="sketch_version" />
                                        </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Nom du Sketch}}</label>
                                        <div class="col-sm-3">
                                            <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="sketch_name" />
                                        </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Etat de la batterie %}}</label>
                                        <div class="col-sm-3">
                                            <input type="text" style="width:100%" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="node_batterie" />
                                        </div>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="type_co" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="col-sm-12 text-center">
                                <img id="img_device" class="img-responsive" height="40%" width="40%" src=""/>
                                </br>
                            </div>
                            <div class="col-sm-12" id="isGatewayAction">
                                <form>
                                    <fieldset>
                                        <legend>{{Inclusion Automatique :}}</legend>
                                        <div class="form-group">
                                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="autoInclude">{{Activer}}</label>
                                        </div>
                                        <a id="btn_include" class="btn btn-success eqLogicAction roundedLeft" data-action="autoInclude"><i id="btn_include" class="fa fa-sign-in fa-rotate-90"></i> {{Activer Inclusion}}</a>
                                    </fieldset>
                                </form>
                                <input type="hidden" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="modeInclude" />
                                <input type="hidden" readonly class="eqLogicAttr btn btn-default btn-sm" data-l1key="configuration" data-l2key="newInclude" />
                            </div>
                        </div>
                    </fieldset>
                </form>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>{{Nom}}</th><th>{{Type}}</th><th style="width: 200px;">{{Paramètres}}</th><th style="width: 100px;">{{Options}}</th><th>{{Action}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'JeeMySensors', 'js', 'JeeMySensors');?>
<?php include_file('core', 'plugin.template', 'js');?>
