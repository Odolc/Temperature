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
    public static function deadCmd()
    {
        $return = array();
        foreach (eqLogic::byType('temperature') as $temperature) {
            foreach ($temperature->getCmd() as $cmd) {
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('infoName', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Temperature', __FILE__) . ' ' . $temperature->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Nom Information', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('calcul', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Temperature', __FILE__) . ' ' . $temperature->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Calcul', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
            }
        }
        return $return;
    }
    public static $_widgetPossibility = array('custom' => true);
    public static function cron5()
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 5 ==================');
                $temperature->getInformations();
            }
        }
    }

    public static function cron10()
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 10 ==================');
                $temperature->getInformations();
            }
        }
    }

    public static function cron15()
    {
        foreach (eqLogic::byType('temperature') as $temperature) {
            if ($temperature->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON 15 ==================');
                $temperature->getInformations();
            }
        }
    }

    public static function cron30()
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
            log::add('temperature', 'debug', '| ───▶︎ CRÉATION COMMANDE : ' . $Name . ' -- Type : ' . $Type . ' -- LogicalID : ' . $_logicalId . ' -- Template Widget / Ligne : ' . $Template . '/' . $forceLineB . '-- Type de générique : ' . $generic_type . ' -- Icône : ' . $icon . ' -- Min/Max : ' . $valuemin . '/' . $valuemax . ' -- Calcul/Arrondi : ' . $_calculValueOffset . '/' . $_historizeRound . ' -- Ordre : ' . $_order);
            $Command = new temperatureCmd();
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
                //log::add('temperature', 'debug', '│ No Repeat pour l\'info avec le nom : ' . $Name);
            }
            if ($valuemin != 'default') {
                $Command->setconfiguration('minValue', $valuemin);
            }
            if ($valuemax != 'default') {
                $Command->setconfiguration('maxValue', $valuemax);
            }

            if ($_order != null) {
                $Command->setOrder($_order);
            }

            $Command->save();
        }

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
                $refresh = new temperatureCmd();
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
    public function preInsert() {}

    public function postInsert() {}

    public function preSave() {}

    public function postSave()
    {

        $order = 0;
        $templatecore_V4  = 'core::';
        $calcul = 'temperature';

        /* spécifique à température */
        $td_num_min = -7;
        $td_num_max = 8;
        $td_num_visible = 0;
        $td_num = 1;
        $template_td = 'default';
        $template_td_num = $templatecore_V4 . 'line';
        $name_td = (__('Message', __FILE__));
        $name_td_num = (__('Message numérique', __FILE__));
        $_iconname_td = 1;
        $_iconname_td_num = null;
        $alert1 = (__('Pré Alerte Humidex', __FILE__));
        $alert2 = (__('Alerte Humidex', __FILE__));

        /* Commun */
        $temp_ressentiename =  (__('Température ressentie', __FILE__));
        $indice_chaleur_name =  (__('Indice de Chaleur (Humidex)', __FILE__));
        $temp_name =  (__('Température', __FILE__));
        $humidity_relative_name =  (__('Humidité Relative', __FILE__));
        $vent_name =  (__('Vitesse du Vent', __FILE__));

        $this->AddCommand($alert1, 'alert_1', 'info', 'binary', $templatecore_V4 . 'line', null, 'SIREN_STATE', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, null, null);
        $this->AddCommand($alert2, 'alert_2', 'info', 'binary', $templatecore_V4 . 'line', null, 'SIREN_STATE', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, null, null);
        $this->AddCommand($temp_ressentiename, 'windchill', 'info', 'numeric', $templatecore_V4 . 'line', '', 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 1, null);
        $this->AddCommand($indice_chaleur_name, 'humidex', 'info', 'numeric', $templatecore_V4 . 'line', null, 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 1, null);
        $this->AddCommand($name_td, 'td', 'info', 'string', $template_td, null, 'WEATHER_CONDITION', $td_num, 'default', 'default', 'default', 'default', $order++, '0', true, $_iconname_td, null, null, null);
        $this->AddCommand($name_td_num, 'td_num', 'info', 'numeric', $template_td_num, null, 'GENERIC_INFO', $td_num_visible, 'default', 'default', $td_num_min, $td_num_max, $order++, '0', true, $_iconname_td_num, null, null, null);
        $this->AddCommand($temp_name, 'temperature', 'info', 'numeric', $templatecore_V4 . 'line', '°C', 'WEATHER_TEMPERATURE', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        $this->AddCommand($humidity_relative_name, 'humidityrel', 'info', 'numeric', $templatecore_V4 . 'line', '%', 'WEATHER_HUMIDITY', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);


        /*  ********************** Vitesse vent *************************** */
        if ($calcul == 'temperature') {
            if ($this->getConfiguration('vent') != '') {
                $this->setConfiguration('wind', $this->getConfiguration('vent'));
                log::add('temperature', 'debug', '| ───▶︎ Modification variable vent pour être aligner avec rosee de vent => wind');
                $this->setConfiguration('vent', null);
                $this->save(true);
            }
            $idvirt = str_replace("#", "", $this->getConfiguration('wind'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $wind_unite = $cmdvirt->getUnite();
            }
            if ($wind_unite == 'm/s') {
                $wind_unite = 'km/h';
            }
        }
        $this->AddCommand($vent_name, 'wind', 'info', 'numeric', $templatecore_V4 . 'line', $wind_unite, 'WEATHER_WIND_SPEED', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        $this->getInformations();
    }

    public function preUpdate()
    {
        if (!$this->getIsEnable()) return;

        if ($this->getConfiguration('temperature') == '') {
            throw new Exception(__((__('Le champ TEMPERATURE ne peut être vide pour l\'équipement : ', __FILE__)) . $this->getName(), __FILE__));
            log::add('temperature', 'error', '│ Configuration : Température inexistant pour l\'équipement : ' . $this->getName() . ' ' . $this->getConfiguration('temperature'));
        }

        if ($this->getConfiguration('humidite') == '') {
            throw new Exception(__((__('Le champ HUMIDITÉ RELATIVE ne peut être vide pour l\'équipement : ', __FILE__)) . $this->getName(), __FILE__));
            log::add('temperature', 'error', '│ Configuration : Humidité Relative inexistant pour l\'équipement : ' . $this->getName() . ' ' . $this->getConfiguration('humidite'));
        }
        if (!isset($this->getConfiguration('vent'))) {
            if ($this->getConfiguration('wind') == '') {
                throw new Exception(__((__('Le champ VITESSE DU VENT ne peut être vide pour l\'équipement : ', __FILE__)) . $this->getName(), __FILE__));
                log::add('temperature', 'error', '│ Configuration : Vitesse du vent inexistant pour l\'équipement : ' . $this->getName() . ' ' . $this->getConfiguration('vent'));
            }
        }
    }


    /*  **********************Getteur Setteur*************************** */
    public function postUpdate() {}

    public function getInformations()
    {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
        log::add('temperature', 'debug', '┌── :fg-success:Configuration de l\'équipement ::/fg: '  . $_eqName . ' ──');

        /*  ********************** Calcul *************************** */
        $calcul = 'temperature';

        /*  ********************** TEMPERATURE *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $idvirt = str_replace("#", "", $this->getConfiguration('temperature'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $temperature = $cmdvirt->execCmd();
            if ($temperature === '') {
                log::add('temperature', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Température', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Température', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
            } else {
                log::add('temperature', 'debug', '| ───▶︎ Température : ' . $temperature . ' °C');
            }
        } else {
            log::add('temperature', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ TEMPERATURE', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
            throw new Exception(__((__('Le champ TEMPERATURE', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
        }
        /*  ********************** Offset Température *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $OffsetT = $this->getConfiguration('OffsetT');
        if ($OffsetT == '') {
            $OffsetT = 0;
        } else {
            $temperature = $temperature + $OffsetT;
        }
        log::add('temperature', 'debug', '| ───▶︎ Température avec Offset : ' . $temperature . ' °C' . ' - Offset Température : ' . $OffsetT . ' °C');

        /*  ********************** VENT *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $idvirt = str_replace("#", "", $this->getConfiguration('wind'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $wind = $cmdvirt->execCmd();
            $wind_unite = $cmdvirt->getUnite();
            if ($wind === '') {
                log::add('temperature', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Vitesse du Vent', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Vitesse du Vent', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
            } else {
                log::add('temperature', 'debug', '| ───▶︎ Vent : ' . $wind . ' ' . $wind_unite);
            }
        } else {
            log::add('temperature', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ VITESSE DU VENT', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
            throw new Exception(__((__('Le champ VITESSE DU VENT', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
        }
        if ($wind_unite == 'm/s') {
            log::add('temperature', 'debug', '| ───▶︎ La vitesse du vent sélectionnée est en m/s, le plugin va convertir en km/h');
            $wind = $wind * 3.6;
            $wind_unite = 'km/h';
            log::add('temperature', 'debug', '| ───▶︎ Vent : ' . $wind  . ' ' . $wind_unite);
        }

        /*  ********************** Seuil PRE-Alerte Humidex*************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $pre_seuil = $this->getConfiguration('PRE_SEUIL');
        if ($pre_seuil === '') {
            $pre_seuil = 30;
            log::add('temperature', 'debug', '| ───▶︎ Aucun Seuil Pré-Alerte Humidex de saisie, valeur par défaut : ' . $pre_seuil . ' °C');
        } else {
            log::add('temperature', 'debug', '| ───▶︎ Seuil Pré-Alerte Humidex : ' . $pre_seuil . ' °C');
        }

        /*  ********************** Seuil Alerte Humidex*************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $seuil = $this->getConfiguration('SEUIL');
        if ($seuil === '') {
            $seuil = 40;
            log::add('temperature', 'debug', '| ───▶︎ Aucun Seuil Alerte Humidex de saisie, valeur par défaut : ' . $seuil . ' °C');
        } else {
            log::add('temperature', 'debug', '| ───▶︎ Seuil Alerte Humidex : ' . $seuil . ' °C');
        }

        /*  ********************** HUMIDITE *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $idvirt = str_replace("#", "", $this->getConfiguration('humidite'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $humidity = $cmdvirt->execCmd();
            if ($humidity === '') {
                log::add('temperature', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Humidité Relative', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Humidité Relative', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
            } else {
                log::add('temperature', 'debug', '| ───▶︎ Humidité Relative : ' . $humidity . ' %');
            }
        } else {
            log::add('temperature', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ HUMIDITÉ RELATIVE', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
            throw new Exception(__((__('Le champ HUMIDITÉ RELATIVE', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
        }

        log::add('temperature', 'debug', '└──');

        /*  ********************** Calcul de la température ressentie *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        if ($calcul == 'temperature') {
            log::add('temperature', 'debug', '┌── :fg-warning:Calcul de la température ressentie ::/fg: '  . $_eqName . ' ──');
            $result_T = temperature::getTemperature($wind, $temperature, $humidity, $pre_seuil, $seuil);
            $windchill = $result_T[0];
            $td = $result_T[1];
            $td_num = $result_T[2];
            $humidex = $result_T[3];
            $alert_1 = $result_T[4];
            $alert_2 = $result_T[5];
            log::add('temperature', 'debug', '└──');
        }

        /*  ********************** Mise à Jour des équipements *************************** */
        log::add('temperature', 'debug', '┌── :fg-success:Mise à jour ::/fg: '  . $_eqName . ' ──');

        $Equipement = eqlogic::byId($this->getId());
        if (is_object($Equipement) && $Equipement->getIsEnable()) {
            $list = 'alert_1,alert_2,humidex,humidityrel,temperature,td,td_num,wind,windchill';
            $Value_calcul = array('alert_1' => $alert_1, 'alert_2' => $alert_2, 'humidex' => $humidex, 'humidityrel' => $humidity, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num, 'wind' => $wind, 'windchill' => $windchill);
            $fields = explode(',', $list);
            foreach ($this->getCmd() as $cmd) {
                foreach ($fields as $fieldname) {
                    if ($cmd->getLogicalId('data') == $fieldname) {
                        $this->checkAndUpdateCmd($fieldname, $Value_calcul[$fieldname]);
                        log::add('temperature', 'debug', '| :fg-info:───▶︎ ' . $cmd->getName() . ' ::/fg: ' . $Value_calcul[$fieldname]);
                    }
                }
            }
            log::add('temperature', 'debug', '└──');
        }
        log::add('temperature', 'debug', '================ FIN CRON OU SAUVEGARDE =================');

        return;
    }
    /*  ********************** Calcul de la température ressentie *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
    public static function getTemperature($wind, $temperature, $humidity, $pre_seuil, $seuil)
    {
        /*  ********************** Calcul du Windchill *************************** */
        //log::add('temperature', 'debug', '│ | ───▶︎ CALCUL DE LA TEMPERATURE RESSENTIE (WINDCHILL)');
        // sources : https://fr.m.wikipedia.org/wiki/Refroidissement_éolien#Calcul
        if ($temperature > 10.0) {
            $windchill = $temperature;
        } else {
            if ($wind >= 4.8) {
                $Rc1 = 13.12 + 0.6215 * $temperature;
                $Rc2 = 0.3965 * $temperature - 11.37;
                $Rc3 = pow($wind, 0.16);
                $windchill = $Rc1 + ($Rc2 * $Rc3);
            } else {
                $Rc2 = 0.1345 * $temperature - 1.59;
                $Rc3 = 0.2 * $Rc2;
                $windchill = $temperature + $Rc3 * $wind;
            }
        }
        log::add('temperature', 'debug', '| ───▶︎ Température ressentie (Windchill) : ' . $windchill . '°C');
        //log::add('temperature', 'debug', '└──');

        /*  ********************** Calcul de l'indice de chaleur *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        //log::add('temperature', 'debug', '│ | ───▶︎ CALCUL DU FACTEUR HUMIDEX');
        // sources : http://www.meteo-mussidan.fr/hum.php
        $var1 = null;
        // Calcul pression vapeur eau
        $temperature_k = $temperature + 273.15;
        log::add('temperature', 'debug', '| ───▶︎ Temperature Kelvin : ' . $temperature_k . ' K');
        // Partage calcul
        $var1 = (-2937.4 / $temperature_k);
        $eTs = pow(10, ($var1 - 4.9283 * log($temperature_k) / 2.302585092994046 + 23.5471));
        $eTd = $eTs * $humidity / 100;
        //Calcul de l'humidex
        $humidex = round($temperature + (($eTd - 10) * 5 / 9));
        if ($humidex  < $temperature) {
            log::add('temperature', 'debug', '| ───▶︎ Indice de Chaleur (Humidex) < Température : ' . $humidex);
            $humidex  = $temperature;
        } else {
            log::add('temperature', 'debug', '| ───▶︎ Indice de Chaleur (Humidex) : ' . $humidex);
        }

        if ($temperature < 10) {
            if (0 < $windchill) {
                $td = (__('Pas de risque de gelures ni d’hypothermie (pour une exposition normale', __FILE__));
                $td_num = -1;
            } else if (-10 < $windchill && 0 <= $windchill) {
                $td = (__('Faible risque de gelures', __FILE__));
                $td_num = -2;
            } else if (-28 < $windchill && -10 <= $windchill) {
                $td = (__('Faible risque de gelures et d’hypothermie', __FILE__));
                $td_num = -3;
            } else if (-40 < $windchill && -28 <= $windchill) {
                $td = (__('Risque modéré de gelures en 10 à 30 minutes de la peau exposée et d’hypothermie', __FILE__));
                $td_num = -4;
            } else if (-48 < $windchill && -40 <= $windchill) {
                $td = (__('Risque élevé de gelures en 5 à 10 minutes (voir note) de la peau exposée et d’hypothermie', __FILE__));
                $td_num = -5;
            } else if (-55 < $windchill && -48 <= $windchill) {
                $td = (__('Risque très élevé de gelures en 2 à 5 minutes (voir note) sans protection intégrale ni activité', __FILE__));
                $td_num = -6;
            } else if ($windchill <= -55) {
                $td = (__('Danger ! Risque extrêmement élevé de gelures en moins de 2 minutes (voir note) et d\'hypothermie. Rester à l\'abri', __FILE__));
                $td_num = -7;
            }
        } else {
            if ($humidex < 15.0) {
                $td = (__('Sensation de frais ou de froid', __FILE__));
                $td_num = 1;
            } elseif ($humidex >= 15.0 && $humidex <= 19.0) {
                $td = (__('Aucun inconfort', __FILE__));
                $td_num = 2;
            } elseif ($humidex > 19.0 && $humidex <= 29.0) {
                $td = (__('Sensation de bien être', __FILE__));
                $td_num = 3;
            } elseif ($humidex > 29.0 && $humidex <= 34.0) {
                $td = (__('Sensation d\'inconfort plus ou moins grande', __FILE__));
                $td_num = 4;
            } elseif ($humidex > 34.0 && $humidex <= 39.0) {
                $td = (__('Sensation d\'inconfort assez grande. Prudence. Ralentir certaines activités en plein air.', __FILE__));
                $td_num = 5;
            } elseif ($humidex > 39.0 && $humidex <= 45.0) {
                $td = (__('Sensation d\'inconfort généralisée. Danger. Éviter les efforts.', __FILE__));
                $td_num = 6;
            } elseif ($humidex > 45.0 && $humidex <= 53.0) {
                $td = (__('Danger extrême. Arrêt de travail dans de nombreux domaines.', __FILE__));
                $td_num = 7;
            } else {
                $td = (__('Coup de chaleur imminent (danger de mort).', __FILE__));
                $td_num = 8;
            }
        }
        //log::add('temperature', 'debug', '│ └─────────');

        /*  ********************** Calcul de l'alerte inconfort indice de chaleur en fonction du seuil d'alerte *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        // log::add('temperature', 'debug', '│ | ───▶︎ ALERTE HUMIDEX');
        if (($humidex) >= $pre_seuil) {
            $alert_1 = 1;
        } else {
            $alert_1 = 0;
        }
        log::add('temperature', 'debug', '| ───▶︎ Seuil Pré-alerte Humidex : ' . $alert_1);

        if (($humidex) >= $seuil) {
            $alert_2 = 1;
        } else {
            $alert_2 = 0;
        }
        log::add('temperature', 'debug', '| ───▶︎ Seuil Alerte Haute Humidex : ' . $alert_2);
        //  log::add('temperature', 'debug', '│ └─────────');


        return array($windchill, $td, $td_num, $humidex, $alert_1, $alert_2);
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
