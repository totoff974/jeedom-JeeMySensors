
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
$('[data-action=save]').on('click', function () {
    if ($('[data-l2key=role]').val() === "Gateway") {
        setTimeout(function(){
            $('#div_alert').showAlert({message: 'Vérification des changements sur le Gateway... Merci de patienter...', level: 'warning'});
            $.showLoading();
        }, 600);
        setTimeout(function(){
            window.location.reload();
        }, 4000);
    }
});

$('[data-action=autoInclude]').on('click', function () {
    if ($('[data-l2key=role]').val() === "Gateway") {
        $('#div_alert').showAlert({message: 'Activation du mode Inclusion... Redémarrer votre Sensor puis patienter...', level: 'warning'});
        $('[data-action=autoInclude]').attr("class", 'btn btn-danger eqLogicAction roundedLeft');
        $.showLoading();
        $.ajax({
            type: "POST",
            url: "plugins/JeeMySensors/core/ajax/JeeMySensors.ajax.php",
            data: {
                action: "inclusionOn",
                id: $('.eqLogicAttr[data-l1key=id]').value(),
            },
            dataType: 'json',
            global: false,
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) {
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
            }
        });
    }
});

$('body').off('JeeMySensors::includeDevice').on('JeeMySensors::includeDevice', function (_event,_options) {
    window.location.href = 'index.php?v=d&m=JeeMySensors&p=JeeMySensors';
    $('#div_alert').showAlert({message: 'Nouvel équipement ajouté avec succès...', level: 'success'});
});

$('[data-l2key=type_gw]').change(function() {
    majDisplayTypeCo();
});

$('[data-l2key=autoInclude]').change(function() {
    majDisplayAutoInclude();
});

$(".eqLogicDisplayCard").off('click').on('click', function () {
    $("#isGateway").hide();
    $("#isGatewayAction").hide();
    $("#isNodeAndGateway").hide();
    $("#isSensor").hide();
    setTimeout(function(){
        majDisplay();
        majImageType()
    }, 500);
});

function majImageType() {
    $image = $('[data-l2key=id_role]').val()
    if ($image != '') {
        $.get('plugins/JeeMySensors/plugin_info/JeeMySensors_' + $image + '.png')
            .done(function(data){
                $('#img_device').attr("src", 'plugins/JeeMySensors/plugin_info/JeeMySensors_' + $image + '.png');
            }).fail(function() {
                $('#img_device').attr("src", 'plugins/JeeMySensors/plugin_info/JeeMySensors_icon.png');
            })
    }
    else {
        $('#img_device').attr("src", 'plugins/JeeMySensors/plugin_info/JeeMySensors_icon.png');
    }
}

function majDisplayTypeCo() {
    $('[data-l2key=ip_gw]').hide();
    $('#type_gw_lan').hide();
    $('#type_gw_serial').hide();
    $('[data-l2key=port_gw]').attr('style', 'display:none');
    mode = $('[data-l2key=type_gw]').val();
    if (mode === 'serial'){
        $('#type_gw_serial').show();
        $('[data-l2key=port_gw]').attr('style', 'display:line');
        $('[data-l2key=ip_gw]').hide();
        $('#type_gw_lan').hide();
    } else if (mode === 'lan'){
        $('#type_gw_serial').hide();
        $('[data-l2key=port_gw]').attr('style', 'display:none');
        $('[data-l2key=ip_gw]').show();
        $('#type_gw_lan').show();
    }
}

function majDisplayAutoInclude() {
    $('#btn_include').hide();
    if ($('[data-l2key=autoInclude]').is(':checked')){
        $('#btn_include').hide();
    } else {
        $('#btn_include').show();
    }
}

function majDisplay() {
    if ($('[data-l2key=id_node]').val() !== "") {
        if ($('[data-l2key=role]').val() === "Gateway" && $('[data-l2key=id_sensor]').val() === "") {
            $("#isSensor").hide();
            $("#isNode").hide();
            $("#isGateway").show("slow");
            $("#isNodeAndGateway").show("slow");
            $("#isGatewayAction").show("slow");
        }
        else if ($('[data-l2key=role]').val() !== "Gateway" && $('[data-l2key=id_sensor]').val() === "255") {
            $("#isGateway").hide();
            $("#isGatewayAction").hide();
            $("#isNode").show("slow");
            $("#isNodeAndGateway").show("slow");
            $("#isSensor").show("slow");
        }
        else {
            $("#isGateway").hide();
            $("#isGatewayAction").hide();
            $("#isNode").hide();
            $("#isNodeAndGateway").hide();
            $("#isSensor").show("slow");
        }
    }
    else {
        $("#isNode").hide();
        $("#isNodeAndGateway").hide();
        $("#isSensor").hide();
        $("#isGateway").show("slow");
        $("#isGatewayAction").show("slow");
    }
}

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="{{La valeur de la commande vaut par défaut la commande}}">';
//    tr += '<option value="">Aucune</option>';
    tr += '</select>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';

    // Add some fields for info and action type
    // Sensor type or command
    tr += '<td>';
        // Sensor type
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="sensorCategory" data-jms-type="info">';
        $.each(jeeMySensorsDictionary['S'], function(index, item) {
            tr += '<option value="' + index + '">' + index + ' - ' + item[0] + '</option>';
        });
        tr += '</select>';
        // Command
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdCommand" data-jms-type="action">';
            $.each(jeeMySensorsDictionary['C'], function(index, item) {
                tr += '<option value="' + index + '"' + (index === 1 ? ' selected="selected"' : '') + '>' + index + ' - ' + item + '</option>';
            });
        tr += '</select>';
    tr += '</td>';

    // Value: request
    tr += '<td>';
        tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="request" placeholder="{{Valeur}}" title="{{Valeur}}" data-jms-type="action"/>';
    tr += '</td>';

    // Sensor data type
    tr += '<td>';
        // Data type for info
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="sensorType" data-jms-type="info">';
        $.each(jeeMySensorsDictionary['V'], function(index, item) {
            tr += '<option value="' + index + '">' + index + ' - ' + item[0] + '</option>';
        });
        tr += '</select>';
        // Data type for action
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdType" data-jms-type="action">';
        $.each(jeeMySensorsDictionary['V'], function(index, item) {
            tr += '<option value="' + index + '">' + index + ' - ' + item[0] + '</option>';
        });
        tr += '</select>';
    tr += '</td>';

    tr += '<td>';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
    tr += ' <input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;display:inline-block;margin-left:2px;">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
        id: $('.eqLogicAttr[data-l1key=id]').value(),
        filter: {type: 'info'},
        error: function (error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result);
            tr.setValues(_cmd, '.cmdAttr');
            jeedom.cmd.changeType(tr, init(_cmd.subType));

            let $type = tr.find('.cmdAttr[data-l1key=type]');
            // Add event
            $type.on('change', typeChangeHandler);
            // Trigger now to force show/hide fields
            typeChangeHandler({currentTarget: $type});

        }
    });
}

/**
 * When type value change
 *
 * @param {Object} e
 */
function typeChangeHandler(e) {
    let $type = $(e.currentTarget);
    let typeValue = $type.value();
    let $tr = $type.parents('tr');

    if (typeValue == 'action') {
        $tr.find('td [data-jms-type=action]').show();
        $tr.find('td [data-jms-type=info]').hide();
    }
    else if (typeValue == 'info') {
        $tr.find('td [data-jms-type=info]').show();
        $tr.find('td [data-jms-type=action]').hide();
    }
}
