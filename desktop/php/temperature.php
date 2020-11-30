<?php

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('temperature');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <!-- Page d'accueil du plugin -->
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
        <!-- Champ de recherche -->
        <div class="input-group" style="margin:5px;">
            <input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
            <div class="input-group-btn">
                <a id="bt_resetSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i> </a>
            </div>
        </div>
        <!-- Liste des équipements du plugin -->
        <legend><i class="jeedom-thermo-moyen"></i> {{Mes Températures ressenties}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            $status = 0;
            foreach ($eqLogics as $eqLogic) {
                $status = 1;
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                echo '<img src="' . $plugin->getPathImgIcon() . '" />';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            if ($status == 1) {
                echo '</div>';
            } else {
                echo "<br/><br/><br/><center><span style='color:#767676;font-size:1em;font-weight: bold;margin-left: 10px'>{{Aucun équipement de type Tendance a été créé.}}</span></center>";
            }
            ?>
        </div>
    </div><!-- /.eqLogicThumbnailDisplay -->
    <!-- Page de présentation de l'équipement -->
    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure" title=" {{Configuration de l'équipement}}"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
                </a><a class="btn btn-warning btn-sm" id="bt_autoDEL_eq" title="{{Recréer les commandes}}"><i class="fas fa-search"></i><span class="hidden-xs"> {{Recréer les commandes}}</span>
                </a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy" title="{{Copier l'équipement}}"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span>
                </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                </a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i><span class="hidden-xs"> {{Supprimer}}</span></a>
            </span>
        </div>
        <!-- Onglets -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content">
            <!-- Onglet de configuration de l'équipement -->
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br />
                <div class="row">
                    <!-- Partie gauche de l'onglet "Equipements" -->
                    <!-- Paramètres généraux de l'équipement -->
                    <div class="col-lg-7">
                        <form class="form-horizontal">
                            <fieldset>
                                <legend> {{}}</legend> <!-- Partie générale non affiché <i class="fas fa-wrench"></i> {{Général}}" -->
                                <div class="form-group ">
                                    <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Objet parent}}</label>
                                    <div class="col-xs-11 col-sm-7">
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
                                    <label class="col-sm-3 control-label">{{Options}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked />{{Activer}}</label>
                                        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked />{{Visible}}</label>
                                    </div>
                                </div>
                                <!-- Paramètres spéficique de l'équipement -->
                                <legend><i class="fas fa-cogs"></i> {{Paramètres}}</legend>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">{{Température}}
                                        <sup><i class="fas fa-question-circle" title="{{(°C) Température}}"></i></sup>
                                    </label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="temperature" placeholder="{{Température}}">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdActionOther roundedRight" title="Rechercher une commande"><i class="fas fa-list-alt"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Offset Température}}
                                        <sup><i class="fas fa-question-circle" title="{{A ajuster en fonction des observations locales et de la position de la sonde, 0 par défaut.}}"></i></sup>
                                    </label>
                                    <div class="col-md-1">
                                        <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="OffsetT" placeholder="0">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Humidité Relative}}
                                        <sup><i class="fas fa-question-circle" title="{{(%) Humidité relative}}"></i></sup>
                                    </label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="humidite" placeholder="{{Humidité Relative}}">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdActionOther roundedRight" title="Rechercher une commande"><i class="fas fa-list-alt"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">{{Vitesse du Vent}}
                                        <sup><i class="fas fa-question-circle" title="{{(km/h) Vitesse du vent}}"></i></sup>
                                    </label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="vent" placeholder="{{Vitesse du vent}}">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdActionOther roundedRight" title="Rechercher une commande"><i class="fas fa-list-alt"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Seuil Pré-alerte Humidex}}
                                        <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de la pré-alerte inconfort de l'indice de température, 30°C par défaut}}"></i></sup>
                                    </label>
                                    <div class="col-md-1">
                                        <input type="number" step="0.1"" class=" eqLogicAttr form-control" data-l1key="configuration" data-l2key="PRE_SEUIL" value="30" placeholder="{{30}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Seuil Alerte Haute Humidex}}
                                        <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de l'alerte inconfort de l'indice de température, 40°C par défaut (seuil de danger)}}"></i></sup>
                                    </label>
                                    <div class="col-md-1">
                                        <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="SEUIL" value="40" placeholder="{{40}}">
                                    </div>
                                </div>

                            </fieldset>
                        </form>
                    </div>
                    <!-- Partie droite de l'onglet "Equipement" -->
                    <!-- Affiche l'icône du plugin par défaut mais vous pouvez y afficher les informations de votre choix -->
                    <div class="col-lg-5">
                        <form class="form-horizontal">
                            <fieldset>
                                <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                                <div class="form-group">
                                    <label class="col-sm-3"></label>
                                    <div class="col-sm-7 text-center">
                                        <img name="icon_visu" src="<?= $plugin->getPathImgIcon(); ?>" style="max-width:160px;" />
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div><!-- /.row-->
            </div> <!-- /.tabpanel #eqlogictab-->
            <!-- Onglet des commandes de l'équipement -->
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <!-- <a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a> -->
                <br /><br />
                <div class="table-responsive">
                    <table id="table_cmd" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>{{Id}}</th>
                                <th>{{Nom}}</th>
                                <th>{{Type}}</th>
                                <th>{{Options}}</th>
                                <th>{{Paramètres}}</th>
                                <th>{{Action}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div><!-- /.tabpanel #commandtab-->

        </div><!-- /.tab-content -->
    </div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<?php
include_file('desktop', 'temperature', 'js', 'temperature');
include_file('core', 'plugin.template', 'js');
?>