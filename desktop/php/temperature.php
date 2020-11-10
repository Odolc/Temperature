<?php

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('temperature');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>
<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i>
                <br />
                <span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i><br>
                <span>{{Configuration}}</span>
            </div>
        </div>
        <legend><i class="jeedom-thermo-moyen"></i> {{Mes Températures ressenties}}</legend>
        <div class="input-group" style="margin:5px;">
            <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
            <div class="input-group-btn">
                <a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i> </a>
            </div>
        </div>
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                echo '<img src="' . $plugin->getPathImgIcon() . '" />';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-warning btn-sm" id="bt_autoDEL_eq"><i class="fas fa-search" title="{{Recréer les commandes}}"></i> {{Recréer les commandes}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
            </span>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>

        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br />
                <form class="form-horizontal col-sm-10">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Nom de l'équipement}}</label>
                            <div class="col-sm-4">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l\'équipement}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Objet parent}}</label>
                            <div class="col-sm-4">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    $options = '';
                                    foreach ((jeeObject::buildTree(null, false)) as $object) {
                                        $decay = $object->getConfiguration('parentNumber');
                                        $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $decay) . $object->getName() . '</option>';
                                    }
                                    echo $options;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Catégorie}}</label>
                            <div class="col-sm-10">
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
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked />{{Activer}}</label>
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked />{{Visible}}</label>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <form class="form-horizontal col-sm-2">
                    <fieldset>
                        <div class="form-group">
                            <img src="<?php echo $plugin->getPathImgIcon(); ?>" style="width:120px;" />
                        </div>
                    </fieldset>
                </form>
                <br />

                <hr>

                <legend><i class="fas fa-cog"></i> {{Paramètres}}</legend>
                <form class="form-horizontal col-sm-10">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 control-label">{{Température}}
                                <sup><i class="fas fa-question-circle" title="{{(°C) Température}}"></i></sup>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="temperature" placeholder="{{Température}}">
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdActionOther roundedRight" id="bt_selectTempCmd"><i class="fas fa-list-alt"></i></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Offset Température}}
                                <sup><i class="fas fa-question-circle" title="{{A ajuster en fonction des observations locales et de la position de la sonde, 0 par défaut.}}"></i></sup>
                            </label>
                            <div class="col-md-1">
                                <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="OffsetT" placeholder="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Humidité Relative}}
                                <sup><i class="fas fa-question-circle" title="{{(%) Humidité relative}}"></i></sup>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="humidite" placeholder="{{Humidité Relative}}">
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdActionOther roundedRight" id="bt_selectHumiCmd"><i class="fas fa-list-alt"></i></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">{{Vitesse du Vent}}
                                <sup><i class="fas fa-question-circle" title="{{(km/h) Vitesse du vent}}"></i></sup>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="vent" placeholder="{{Vitesse du vent}}">
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdActionOther roundedRight" id="bt_selectWindCmd"><i class="fas fa-list-alt"></i></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Seuil Pré-alerte Humidex}}
                                <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de la pré-alerte inconfort de l'indice de température, 30°C par défaut}}"></i></sup>
                            </label>
                            <div class="col-md-1">
                                <input type="number" step="0.1"" class=" eqLogicAttr form-control" data-l1key="configuration" data-l2key="PRE_SEUIL" value="30" placeholder="{{30}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Seuil Alerte Haute Humidex}}
                                <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de l'alerte inconfort de l'indice de température, 40°C par défaut (seuil de danger)}}"></i></sup>
                            </label>
                            <div class="col-md-1">
                                <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="SEUIL" value="40" placeholder="{{40}}">
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div role="tabpanel" class="tab-pane" id="commandtab">
                <br />
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 50px;"> ID</th>
                            <th style="width: 550px;">{{Nom}}</th>
                            <th style="width: 250px;">{{Sous-Type}}</th>
                            <th style="width: 350px;">{{Min/Max - Unité}}</th>
                            <th>{{Paramètres}}</th>
                            <th style="width: 250px;">{{Options}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php
include_file('desktop', 'temperature', 'js', 'temperature');
include_file('core', 'plugin.template', 'js');
?>