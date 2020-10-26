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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../core/php/temperature.inc.php';

class temperature extends eqLogic
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
    public static function cron5($_eqlogic_id = null)
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 5 ==================');
                $temperature->getInformations();
            }
        }
    }

    public static function cron10($_eqlogic_id = null)
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 10 ==================');
                $temperature->getInformations();
            }
        }
    }

    public static function cron15($_eqlogic_id = null)
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 15 ==================');
                $temperature->getInformations();
            }
        }
    }

    public static function cron30($_eqlogic_id = null)
    {
        //no both cron5 and cron30 enabled:
        if (config::byKey('functionality::cron15::enable', 'temperature', 0) == 1) {
            config::save('functionality::cron30::enable', 0, 'temperature');
            return;
        }
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 30 =================');
                $temperature->getInformations();
            }
        }
    }

    public static function cronHourly()
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON HEURE =================');
                $temperature->getInformations();
            }
        }
    }
    public function AddCommand($Name, $_logicalId, $Type = 'info', $SubType = 'binary', $Template = null, $unite = null, $generic_type = null, $IsVisible = 1, $icon = 'default', $forceLineB = 'default', $valuemin = 'default', $valuemax = 'default', $_order = null, $IsHistorized = '0', $repeatevent = false, $_iconname = null, $_calculValueOffset = null, $_historizeRound = null, $_noiconname = null)
    {

        $Command = $this->getCmd(null, $_logicalId);
        if (!is_object($Command)) {
            log::add(__CLASS__, 'debug', '│ Name : ' . $Name . ' -- Type : ' . $Type . ' -- LogicalID : ' . $_logicalId . ' -- Template Widget / Ligne : ' . $Template . '/' . $forceLineB . '-- Type de générique : ' . $generic_type . ' -- Icône : ' . $icon . ' -- Min/Max : ' . $valuemin . '/' . $valuemax . ' -- Calcul/Arrondi : ' . $_calculValueOffset . '/' . $_historizeRound . ' -- Ordre : ' . $_order);
            $Command = new roseeCmd();
            $Command->setId(null);
            $Command->setLogicalId($_logicalId);
            $Command->setEqLogic_id($this->getId());
            $Command->setName($Name);

            $Command->setType($Type);
            $Command->setSubType($SubType);

            if ($Template != null) {
                $Command->setTemplate('dashboard', $Template);
                $Command->setTemplate('mobile', $Template);
            }

            if ($unite != null && $SubType == 'numeric') {
                $Command->setUnite($unite);
            }

            $Command->setIsVisible($IsVisible);
            $Command->setIsHistorized($IsHistorized);

            if ($icon != 'default') {
                $Command->setdisplay('icon', '<i class="' . $icon . '"></i>');
            }
            if ($forceLineB != 'default') {
                $Command->setdisplay('forceReturnLineBefore', 1);
            }
            if ($_iconname != 'default') {
                $Command->setdisplay('showIconAndNamedashboard', 1);
            }
            if ($_noiconname != null) {
                $Command->setdisplay('showNameOndashboard', 0);
            }

            if ($_calculValueOffset != null) {
                $Command->setConfiguration('calculValueOffset', $_calculValueOffset);
            }

            if ($_historizeRound != null) {
                $Command->setConfiguration('historizeRound', $_historizeRound);
            }
            if ($generic_type != null) {
                $Command->setGeneric_type($generic_type);
            }

            if ($repeatevent == true && $Type == 'info') {
                $Command->setconfiguration('repeatEventManagement', 'never');
                log::add(__CLASS__, 'debug', '│ No Repeat pour l\'info avec le nom : ' . $Name);
            }
            if ($valuemin != 'default') {
                $Command->setconfiguration('minValue', $valuemin);
            }
            if ($valuemax != 'default') {
                $Command->setconfiguration('maxValue', $valuemax);
            }

            $Command->save();
        }

        if ($_order != null) {
            $Command->setOrder($_order);
        }

        $Command->save();

        $createRefreshCmd = true;
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = cmd::byEqLogicIdCmdName($this->getId(), __('Rafraichir', __FILE__));
            if (is_object($refresh)) {
                $createRefreshCmd = false;
            }
        }
        if ($createRefreshCmd) {
            if (!is_object($refresh)) {
                $refresh = new roseeCmd();
                $refresh->setLogicalId('refresh');
                $refresh->setIsVisible(1);
                $refresh->setName(__('Rafraichir', __FILE__));
            }
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setEqLogic_id($this->getId());
            $refresh->save();
        }
        return $Command;
    }
    /*     * *********************Methode d'instance************************* */
    public function refresh()
    {
        foreach ($this->getCmd() as $cmd) {
            $s = print_r($cmd, 1);
            log::add('temperature', 'debug', 'refresh  cmd: ' . $s);
            $cmd->execute();
        }
    }


    public function preUpdate()
    {
        if (!$this->getIsEnable()) return;

        if ($this->getConfiguration('temperature') == '') {
            throw new Exception(__('Le champ "Température" ne peut etre vide', __FILE__));
        }

        if ($this->getConfiguration('humidite') == '') {
            throw new Exception(__('Le champ "Humidité Relative" ne peut etre vide', __FILE__));
        }

        if ($this->getConfiguration('vent') == '') {
            throw new Exception(__('Le champ "Vitesse du Vent" ne peut etre vide', __FILE__));
        }
    }

    public function postInsert()
    {
    }

    public function postSave()
    {
        $_eqName = $this->getName();
        log::add('temperature', 'debug', 'postSave() =>' . $_eqName);
        $order = 1;

        if (version_compare(jeedom::version(), "4", "<")) {
            $templatecore_V4 = null;
        } else {
            $templatecore_V4  = 'core::';
        };

        $Equipement = eqlogic::byId($this->getId());
        $Equipement->AddCommand('Windchill', 'windchill', 'info', 'numeric', $templatecore_V4 . 'line', '°C', 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order, '0', true, 'default', null, 1, null);
        $order++;
        $Equipement->AddCommand('Indice de chaleur', 'heat_index', 'info', 'numeric', $templatecore_V4 . 'multiline', '°C', 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order, '0', true, 'default', null, 1, null);
        $order++;
        $Equipement->AddCommand('Pré Alerte Humidex', 'alert_1', 'info', 'binary', $templatecore_V4 . 'line', null, 'SIREN_STATE', 1, 'default', 'default', 'default', 'default', $order, '0', true, 'default', null, null, null);
        $order++;
        $Equipement->AddCommand('Alerte Humidex', 'alert_2', 'info', 'binary', $templatecore_V4 . 'line', null, 'SIREN_STATE', 1, 'default', 'default', 'default', 'default', $order, '0', true, 'default', null, null, null);
        $order++;
        $Equipement->AddCommand('Message', 'td', 'info', 'string', $templatecore_V4 . 'Multiline', null, 'GENERIC_INFO', 1, 'default', 'default', 'default', 'default', $order, '0', true, null, 'default', null, null);
    }



    /*  **********************Getteur Setteur*************************** */
    public function postUpdate()
    {
        $this->getInformations();
    }

    public function getInformations()
    {
        if (!$this->getIsEnable()) return;

        $_eqName = $this->getName();
        log::add('temperature', 'debug', '┌───────── CONFIGURATION EQUIPEMENT : ' . $_eqName);

        /*  ********************** TEMPERATURE *************************** */
        $idvirt = str_replace("#", "", $this->getConfiguration('temperature'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $temperature = $cmdvirt->execCmd();
            log::add('temperature', 'debug', '│ Température : ' . $temperature . ' °C');
        } else {
            throw new Exception(__('Le champ "Température" ne peut être vide', __FILE__));
            log::add('temperature', 'error', 'Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
        }

        /*  ********************** HUMIDITE *************************** */
        $idvirt = str_replace("#", "", $this->getConfiguration('humidite'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $humidity = $cmdvirt->execCmd();
            log::add('temperature', 'debug', '│ Humidité Relative : ' . $humidity . ' %');
        } else {
            throw new Exception(__('Le champ "Humidité Relative" ne peut être vide', __FILE__));
            log::add('temperature', 'error', 'Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
        }

        /*  ********************** VENT *************************** */
        $idvirt = str_replace("#", "", $this->getConfiguration('vent'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $vent = $cmdvirt->execCmd();
            $unite = $cmdvirt->getUnite();
            log::add('temperature', 'debug', '│ Vent : ' . $vent . ' ' . $unite);
        } else {
            throw new Exception(__('Le champ "Vitesse du Vent" ne peut être vide', __FILE__));
            log::add('temperature', 'error', 'Configuration : vent non existant : ' . $this->getConfiguration('vent'));
        }
        if ($unite == 'm/s') {
            log::add('temperature', 'debug', '│ La vitesse du vent sélectionnée est en m/s, le plugin va convertir en km/h');
            $vent = $vent * 3.6;
            log::add('temperature', 'debug', '│ Vent : ' . $vent . ' km/h');
        }


        /*  ********************** Seuil Alerte Humidex*************************** */
        $seuil = $this->getConfiguration('SEUIL');
        if ($seuil == '') {
            $seuil = 40;
            log::add('temperature', 'debug', '│ Aucun Seuil Alerte Humidex de saisie, valeur par défaut : ' . $seuil . ' °C');
        } else {
            log::add('temperature', 'debug', '│ Seuil Alerte Humidex : ' . $seuil . ' °C');
        }

        /*  ********************** Seuil Alerte Humidex*************************** */
        $pre_seuil = $this->getConfiguration('PRE_SEUIL');
        if ($pre_seuil == '') {
            $pre_seuil = 30;
            log::add('temperature', 'debug', '│ Aucun Seuil Pré-Alerte Humidex de saisie, valeur par défaut : ' . $pre_seuil . ' °C');
        } else {
            log::add('temperature', 'debug', '│ Seuil Pré-Alerte Humidex : ' . $pre_seuil . ' °C');
        }
        log::add('temperature', 'debug', '└─────────');


        /*  ********************** Calcul du Windchill *************************** */
        log::add('temperature', 'debug', '┌───────── CALCUL DU WINDCHILL : ' . $_eqName);

        if ($temperature > 10.0) {
            $windchill = $temperature;
        } else {
            if ($vent >= 4.8) {
                $Terme1 = 13.12 + 0.6215 * $temperature;
                $Terme2 = 0.3965 * $temperature - 11.37;
                $Terme3 = pow($vent, 0.16);
                $windchill = $Terme2 * $Terme3 + $Terme1;
            } else {
                $windchill = $temperature + 0.2 * (0.1345 * $temperature - 1.59) * $vent;
            }
        }
        log::add('temperature', 'debug', '│ Windchill : ' . $windchill . '°C');
        log::add('temperature', 'debug', '└───────');

        /*  ********************** Calcul de l'indice de chaleur *************************** */
        log::add('temperature', 'debug', '┌───────── CALCUL DU FACTEUR HUMIDEX : ' . $_eqName);
        $c1 = -42.379;
        $c2 = 2.04901523;
        $c3 = 10.14333127;
        $c4 = -0.22475541;
        $c5 = -6.83783 * pow(10, -3);
        $c6 = -5.481717 * pow(10, -2);
        $c7 = 1.22874 * pow(10, -3);
        $c8 = 8.5282 * pow(10, -4);
        $c9 = -1.99 * pow(10, -6);
        $tempF = 32.0 + 1.8 * $temperature;
        log::add('temperature', 'debug', '│ Température (F) : ' . $tempF . ' F');
        $terme1 = $c1 + $c2 * $tempF + $c3 * $humidity + $c4 * $tempF * $humidity;
        $terme2 = $c5 * pow($tempF, 2.0);
        $terme3 = $c6 * pow($humidity, 2.0);
        $terme4 = $c7 * $humidity * pow($tempF, 2.0);
        $terme5 = $c8 * $tempF * pow($humidity, 2.0);
        $terme6 = $c9 * pow($tempF, 2.0) * pow($humidity, 2.0);
        $heat_index_F = $terme1 + $terme2 + $terme3 + $terme4 + $terme5 + $terme6;
        $heat_index = ($heat_index_F - 32.0) / 1.8;
        log::add('temperature', 'debug', '│ Indice de Chaleur (Humidex) : ' . $heat_index . ' °C');

        if ($heat_index < 15.0) {
            $td = 'Sensation de frais ou de froid';
        } elseif ($heat_index >= 15.0 && $heat_index <= 19.0) {
            $td = 'Aucun inconfort';
        } elseif ($heat_index > 19.0 && $heat_index <= 29.0) {
            $td = "Sensation de bien être";
        } elseif ($heat_index > 29.0 && $heat_index <= 34.0) {
            $td = "Sensation d'inconfort plus ou moins grande";
        } elseif ($heat_index > 34.0 && $heat_index <= 39.0) {
            $td = "Sensation d'inconfort assez grande. Prudence. Ralentir certaines activités en plein air.";
        } elseif ($heat_index > 39.0 && $heat_index <= 45.0) {
            $td = "Sensation d'inconfort généralisée. Danger. Éviter les efforts.";
        } elseif ($heat_index > 45.0 && $heat_index <= 53.0) {
            $td = 'Danger extrême. Arrêt de travail dans de nombreux domaines.';
        } else {
            $td = 'Coup de chaleur imminent (danger de mort).';
        }
        log::add('temperature', 'debug', '└─────────');

        /*  ********************** Calcul de l'alerte inconfort indice de chaleur en fonction du seuil d'alerte *************************** */
        log::add('temperature', 'debug', '┌───────── ALERTE HUMIDEX : ' . $_eqName);
        if (($heat_index) >= $seuil) {
            $alert_2 = 1;
        } else {
            $alert_2 = 0;
        }
        log::add('temperature', 'debug', '│ Seuil Alerte Haute Humidex : ' . $alert_2);

        if (($heat_index) >= $pre_seuil) {
            $alert_1 = 1;
        } else {
            $alert_1 = 0;
        }
        log::add('temperature', 'debug', '│ Seuil Pré-alerte Humidex : ' . $alert_1);
        log::add('temperature', 'debug', '└─────────');

        /*  ********************** Mise à Jour des équipements *************************** */
        log::add('temperature', 'debug', '┌───────── MISE A JOUR : ' . $_eqName);

        $Equipement = eqlogic::byId($this->getId());
        if (is_object($Equipement) && $Equipement->getIsEnable()) {

            foreach ($Equipement->getCmd('info') as $Command) {
                if (is_object($Command)) {
                    switch ($Command->getLogicalId()) {
                        case "windchill": //Mise à jour de la commande Winchill
                            log::add(__CLASS__, 'debug', '│ Windchill : ' . $windchill . ' °C');
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $windchill);
                            break;
                        case "td": //Mise à jour de la commande tendance
                            log::add(__CLASS__, 'debug', '│ Degré de comfort : ' . $td);
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $td);
                            break;
                        case "heat_index": //Mise à jour de la commande Facteur Humidex
                            log::add(__CLASS__, 'debug', '│ Facteur Humidex : ' . $heat_index . ' °C');
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $heat_index);
                            break;
                        case "alert_1": //Mise à jour de la commande Alerte 1
                            log::add(__CLASS__, 'debug', '│ Etat Pré-alerte Humidex : ' . $alert_1);
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $alert_1);
                            break;
                        case "alert_2": //Mise à jour de la commande Alerte 1
                            log::add(__CLASS__, 'debug', '│ Etat Alerte Haute Humidex : ' . $alert_2);
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $alert_2);
                            break;
                    }
                }
            }
        }
        log::add('temperature', 'debug', '└─────────');
        log::add('temperature', 'debug', '================ FIN CRON =================');

        return;
    }
}

class temperatureCmd extends cmd
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
    public function dontRemoveCmd()
    {
        if ($this->getLogicalId() == 'refresh') {
            return true;
        }
        return false;
    }

    public function execute($_options = null)
    {
        if ($this->getLogicalId() == 'refresh') {
            log::add('temperature', 'debug', ' ─────────> ACTUALISATION MANUELLE');
            $this->getEqLogic()->getInformations();
            log::add('temperature', 'debug', ' ─────────> FIN ACTUALISATION MANUELLE');
            return;
        }
    }
}
