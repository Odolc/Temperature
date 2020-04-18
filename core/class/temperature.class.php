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

class temperature extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
	public static function cron5($_eqlogic_id = null) {
        foreach (eqLogic::byType('temperature') as $temperature) {
			if ($temperature->getIsEnable()) {
				log::add('temperature', 'debug', '================= CRON 5 ==================');
				$temperature->getInformations();
			}
		}
	}

    public static function cron10($_eqlogic_id = null) {
        foreach (eqLogic::byType('temperature') as $temperature) {
			if ($temperature->getIsEnable()) {
				log::add('temperature', 'debug', '================= CRON 10 ==================');
				$temperature->getInformations();
			}
		}
	}

    public static function cron15($_eqlogic_id = null) {
        foreach (eqLogic::byType('temperature') as $temperature) {
			if ($temperature->getIsEnable()) {
				log::add('temperature', 'debug', '================= CRON 15 ==================');
				$temperature->getInformations();
			}
		}
	}

  	public static function cron30($_eqlogic_id = null) {
		//no both cron5 and cron30 enabled:
        if (config::byKey('functionality::cron15::enable', 'temperature', 0) == 1)
        {
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

    public static function cronHourly() {
        foreach (eqLogic::byType('temperature') as $rosee) {
            if ($rosee->getIsEnable()) {
                log::add('temperature', 'debug', '================= CRON HEURE =================');
                $rosee->getInformations();
            }
        }
    }

    /*     * *********************Methode d'instance************************* */
    public function refresh() {
        foreach ($this->getCmd() as $cmd)
        {
            $s = print_r($cmd, 1);
            log::add('temperature', 'debug', 'refresh  cmd: '.$s);
            $cmd->execute();
        }
    }


    public function preUpdate() {
        if (!$this->getIsEnable()) return;

    	if ($this->getConfiguration('temperature') == '') {
    		throw new Exception(__('Le champ "Température" ne peut etre vide',__FILE__));
        }

        if ($this->getConfiguration('humidite') == '') {
        	throw new Exception(__('Le champ "Humidité Relative" ne peut etre vide',__FILE__));
    	}

    	if ($this->getConfiguration('vent') == '') {
        	throw new Exception(__('Le champ "Vitesse du Vent" ne peut etre vide',__FILE__));
    	}
    }

    public function postInsert() {

	}

  	public function postSave() {
        log::add('temperature', 'debug', 'postSave()');
        $order = 1;

        // Ajout d'une commande dans le tableau pour le windchill
        $temperatureCmd= $this->getCmd(null, 'windchill');
        if (!is_object($temperatureCmd)) {
            $temperatureCmd = new temperatureCmd();
            $temperatureCmd->setName(__('Windchill', __FILE__));
            $temperatureCmd->setEqLogic_id($this->id);
            $temperatureCmd->setLogicalId('windchill');
            $temperatureCmd->setConfiguration('data', 'windchill');
            $temperatureCmd->setType('info');
            $temperatureCmd->setSubType('numeric');
            $temperatureCmd->setIsHistorized(0);
            $temperatureCmd->setTemplate('dashboard','core::line');
            $temperatureCmd->setTemplate('mobile','core::line');
            $temperatureCmd->setOrder($order);
            $order ++;
        }
        $temperatureCmd->setEqLogic_id($this->getId());
        $temperatureCmd->setUnite('°C');
        $temperatureCmd->setDisplay('generic_type','GENERIC_INFO');
        $temperatureCmd->setType('info');
        $temperatureCmd->setSubType('numeric');
        $temperatureCmd->setTemplate('dashboard','core::line');
        $temperatureCmd->setTemplate('mobile','core::line');
        $temperatureCmd->save();

        // Ajout d'une commande dans le tableau pour l'indice de chaleur
        $temperatureCmd= $this->getCmd(null, 'heat_index');
        if (!is_object($temperatureCmd)) {
            $temperatureCmd = new temperatureCmd();
            $temperatureCmd->setName(__('Indice de chaleur', __FILE__));
            $temperatureCmd->setEqLogic_id($this->id);
            $temperatureCmd->setLogicalId('heat_index');
            $temperatureCmd->setConfiguration('data', 'heat_index');
            $temperatureCmd->setType('info');
            $temperatureCmd->setSubType('numeric');
            $temperatureCmd->setUnite('°C');
            $temperatureCmd->setIsHistorized(0);
            $temperatureCmd->setIsVisible(0);
            $temperatureCmd->setTemplate('dashboard','core::multiline');
            $temperatureCmd->setTemplate('mobile','core::multiline');
            $temperatureCmd->setOrder($order);
            $order ++;
        }
        $temperatureCmd->setEqLogic_id($this->getId());
        $temperatureCmd->setUnite('°C');
        $temperatureCmd->setDisplay('generic_type','GENERIC_INFO');
        $temperatureCmd->setType('info');
        $temperatureCmd->setSubType('numeric');
        $temperatureCmd->save();

        // Ajout d'une commande dans le tableau pour l'alerte inconfort
        $temperatureCmd= $this->getCmd(null, 'alert_2');
        if (!is_object($temperatureCmd)) {
            $temperatureCmd = new temperatureCmd();
            $temperatureCmd->setName(__('Alerte Humidex', __FILE__));
            $temperatureCmd->setEqLogic_id($this->id);
            $temperatureCmd->setLogicalId('alert_2');
            $temperatureCmd->setConfiguration('data', 'alert_2');
            $temperatureCmd->setType('info');
            $temperatureCmd->setSubType('binary');
            $temperatureCmd->setUnite('');
            $temperatureCmd->setIsHistorized(0);
            $temperatureCmd->setTemplate('dashboard','core::line');
            $temperatureCmd->setTemplate('mobile','core::line');
            $temperatureCmd->setOrder($order);
            $order ++;
        }
        $temperatureCmd->setEqLogic_id($this->getId());
        $temperatureCmd->setDisplay('generic_type','SIREN_STATE');
        $temperatureCmd->setType('info');
        $temperatureCmd->setSubType('binary');
        $temperatureCmd->save();

        // Ajout d'une commande dans le tableau pour la  pré-alerte inconfort
        $temperatureCmd= $this->getCmd(null, 'alert_1');
        if (!is_object($temperatureCmd)) {
            $temperatureCmd = new temperatureCmd();
            $temperatureCmd->setName(__('Pré Alerte Humidex', __FILE__));
            $temperatureCmd->setEqLogic_id($this->id);
            $temperatureCmd->setLogicalId('alert_1');
            $temperatureCmd->setConfiguration('data', 'alert_1');
            $temperatureCmd->setType('info');
            $temperatureCmd->setSubType('binary');
            $temperatureCmd->setUnite('');
            $temperatureCmd->setIsHistorized(0);
            $temperatureCmd->setTemplate('dashboard','core::line');
            $temperatureCmd->setTemplate('mobile','core::line');
            $temperatureCmd->setOrder($order);
            $order ++;
        }
        $temperatureCmd->setEqLogic_id($this->getId());
        $temperatureCmd->setDisplay('generic_type','SIREN_STATE');
        $temperatureCmd->setType('info');
        $temperatureCmd->setSubType('binary');
        $temperatureCmd->save();

        // Ajout d'une commande dans le tableau pour l'info inconfort
        $temperatureCmd= $this->getCmd(null, 'td');
        if (!is_object($temperatureCmd)) {
            $temperatureCmd = new temperatureCmd();
            $temperatureCmd->setName(__('Message inconfort', __FILE__));
            $temperatureCmd->setEqLogic_id($this->id);
            $temperatureCmd->setLogicalId('td');
            $temperatureCmd->setConfiguration('data', 'td');
            $temperatureCmd->setType('info');
            $temperatureCmd->setSubType('string');
            $temperatureCmd->setUnite('');
            $temperatureCmd->setIsHistorized(0);
            $temperatureCmd->setIsVisible(0);
            $temperatureCmd->setDisplay('generic_type','GENERIC_INFO');
            $temperatureCmd->setTemplate('dashboard','core::Multiline');
            $temperatureCmd->setTemplate('mobile','core::Multiline');
            $temperatureCmd->setOrder($order);
            $order ++;
        }
        $temperatureCmd->setEqLogic_id($this->getId());
        $temperatureCmd->setUnite('');
        $temperatureCmd->setDisplay('generic_type','GENERIC_INFO');
        $temperatureCmd->setType('info');
        $temperatureCmd->setSubType('string');
        $temperatureCmd->save();

        $refresh = $this->getCmd(null, 'refresh');
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



	/*  **********************Getteur Setteur*************************** */
	public function postUpdate() {
        foreach (eqLogic::byType('temperature') as $temperature) {
            $temperature->getInformations();
        }
	}

	public function getInformations() {
        if (!$this->getIsEnable()) return;

        $_eqName = $this->getName();
		log::add('temperature', 'debug', '┌───────── CONFIGURATION EQUIPEMENT : '.$_eqName );

        /*  ********************** TEMPERATURE *************************** */
        $idvirt = str_replace("#","",$this->getConfiguration('temperature'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $temperature = $cmdvirt->execCmd();
            log::add('temperature', 'debug', '│ Température : ' . $temperature.' °C');
        } else {
            throw new Exception(__('Le champ "Température" ne peut être vide',__FILE__));
            log::add('temperature', 'error', 'Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
        }

        /*  ********************** HUMIDITE *************************** */
        $idvirt = str_replace("#","",$this->getConfiguration('humidite'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $humidity = $cmdvirt->execCmd();
            log::add('temperature', 'debug', '│ Humidité Relative : ' . $humidity.'  %');
        } else {
            throw new Exception(__('Le champ "Humidité Relative" ne peut être vide',__FILE__));
            log::add('temperature', 'error', 'Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
        }

        /*  ********************** VENT *************************** */
        $idvirt = str_replace("#","",$this->getConfiguration('vent'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $vent = $cmdvirt->execCmd();
            $unite = $cmdvirt->getUnite();
            log::add('temperature', 'debug', '│ Vent : ' . $vent.' ' .$unite);
        } else {
            throw new Exception(__('Le champ "Vitesse du Vent" ne peut être vide',__FILE__));
            log::add('temperature', 'error', 'Configuration : vent non existant : ' . $this->getConfiguration('vent'));
        }
        if ($unite == 'm/s') {
            log::add('temperature', 'debug', '│ La vitesse du vent sélectionnée est en m/s, le plugin va convertir en km/h');
            $vent = $vent * 3.6;
            log::add('temperature', 'debug', '│ Vent : ' . $vent.' km/h');
        }


        /*  ********************** Seuil Alerte Humidex*************************** */
        $seuil=$this->getConfiguration('SEUIL');
        if ($seuil == '') {
            $seuil=40;
            log::add('temperature', 'debug', '│ Aucun Seuil Alerte Humidex de saisie, valeur par défaut : ' . $seuil.' °C');
        } else {
            log::add('temperature', 'debug', '│ Seuil Alerte Humidex : ' . $seuil. ' °C');
        }

		/*  ********************** Seuil Alerte Humidex*************************** */
        $pre_seuil=$this->getConfiguration('PRE_SEUIL');
        if ($pre_seuil == '') {
            $pre_seuil=30;
            log::add('temperature', 'debug', '│ Aucun Seuil Pré-Alerte Humidex de saisie, valeur par défaut : '. $pre_seuil. ' °C');
        } else {
            log::add('temperature', 'debug', '│ Seuil Pré-Alerte Humidex : ' . $pre_seuil. ' °C');
        }
        log::add('temperature', 'debug', '└─────────');


        /*  ********************** Calcul du Windchill *************************** */
        log::add('temperature', 'debug', '┌───────── CALCUL DU WINDCHILL : '.$_eqName);

        if($temperature > 10.0) {
            $windchill = $temperature;
        } else {
            if($vent >= 4.8) {
                $Terme1 = 13.12 + 0.6215 * $temperature;
                $Terme2 = 0.3965 * $temperature - 11.37;
                $Terme3 = pow($vent,0.16);
                $windchill = $Terme2 * $Terme3 + $Terme1;
            } else {
                $windchill = $temperature + 0.2 * (0.1345 * $temperature - 1.59) * $vent;
            }
        }
        $windchill = round(($windchill), 1);
        log::add('temperature', 'debug', '│ Windchill : ' . $windchill.'°C');
        log::add('temperature', 'debug', '└───────');

        /*  ********************** Calcul de l'indice de chaleur *************************** */
        log::add('temperature', 'debug', '┌───────── CALCUL DU FACTEUR HUMIDEX : '.$_eqName);
        $c1 = -42.379;
        $c2 = 2.04901523;
        $c3 = 10.14333127;
        $c4 = -0.22475541;
        $c5 = -6.83783 * pow(10,-3);
        $c6 = -5.481717 * pow(10,-2);
        $c7 = 1.22874 * pow(10,-3);
        $c8 = 8.5282 * pow(10,-4);
        $c9 = -1.99 * pow(10,-6);
        $tempF = 32.0 + 1.8 * $temperature;
        log::add('temperature', 'debug', '│ Température (F) : ' . $tempF.' °F');
        $terme1 = $c1 + $c2 * $tempF + $c3 * $humidity + $c4 * $tempF * $humidity;
        $terme2 = $c5 * pow($tempF,2.0);
        $terme3 = $c6 * pow($humidity,2.0);
        $terme4 = $c7 * $humidity * pow($tempF,2.0);
        $terme5 = $c8 * $tempF * pow($humidity,2.0);
        $terme6 = $c9 * pow($tempF,2.0) * pow($humidity,2.0);
        $heat_index_F = $terme1 + $terme2 + $terme3 + $terme4 + $terme5 + $terme6;
        $heat_index = ($heat_index_F - 32.0) / 1.8;
        $heat_index = round(($heat_index), 1);
        log::add('temperature', 'debug', '│ Indice de Chaleur (Humidex) : ' . $heat_index. ' °C');

        if($heat_index < 15.0) {
            $td = 'Sensation de frais ou de froid';
        }elseif($heat_index >= 15.0 && $heat_index <= 19.0) {
            $td = 'Aucun inconfort';
        }elseif($heat_index > 19.0 && $heat_index <= 29.0) {
            $td = "Sensation de bien être";
        }elseif($heat_index > 29.0 && $heat_index <= 34.0) {
            $td = "Sensation d'inconfort plus ou moins grande";
        }elseif($heat_index > 34.0 && $heat_index <= 39.0) {
            $td = "Sensation d'inconfort assez grande. Prudence. Ralentir certaines activités en plein air.";
        }elseif($heat_index > 39.0 && $heat_index <= 45.0) {
            $td = "Sensation d'inconfort généralisée. Danger. Éviter les efforts.";
        }elseif($heat_index > 45.0 && $heat_index <= 53.0) {
            $td = 'Danger extrême. Arrêt de travail dans de nombreux domaines.';
        }else {
            $td = 'Coup de chaleur imminent (danger de mort).';
        }
        log::add('temperature', 'debug', '└─────────');

        /*  ********************** Calcul de l'alerte inconfort indice de chaleur en fonction du seuil d'alerte *************************** */
        log::add('temperature', 'debug', '┌───────── ALERTE HUMIDEX : '.$_eqName);
        if(($heat_index) >= $seuil) {
            $alert_2 = 1;
        } else {
            $alert_2 = 0;
        }
        log::add('temperature', 'debug', '│ Seuil Alerte Haute Humidex : ' . $alert_2);

        if(($heat_index) >= $pre_seuil) {
            $alert_1 = 1;
        } else {
            $alert_1 = 0;
        }
        log::add('temperature', 'debug', '│ Seuil Pré-alerte Humidex : ' . $alert_1);
        log::add('temperature', 'debug', '└─────────');

        /*  ********************** Mise à Jour des équipements *************************** */
        log::add('temperature', 'debug', '┌───────── MISE A JOUR : '.$_eqName);

        $cmd = $this->getCmd('info', 'windchill');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $windchill);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($windchill);
            log::add('temperature', 'debug', '│ Windchill : ' . $windchill.' °C');
		}

        $cmd = $this->getCmd('info', 'td');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $td);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($td);
            log::add('temperature', 'debug', '│ Degré de comfort : ' . $td. '');
		}

        $cmd = $this->getCmd('info', 'heat_index');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $heat_index);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($heat_index);
            log::add('temperature', 'debug', '│ Facteur Humidex : ' . $heat_index.' °C');
		}

        $cmd = $this->getCmd('info', 'alert_1');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_1);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($alert_1);
            log::add('temperature', 'debug', '│ Etat Pré-alerte Humidex : ' . $alert_1. '');
		}

        $cmd = $this->getCmd('info', 'alert_2');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_2);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($alert_2);
            log::add('temperature', 'debug', '│ Etat Alerte Haute Humidex : ' . $alert_2. '');
		}


        log::add('temperature', 'debug', '└─────────');
        log::add('temperature', 'debug', '================ FIN CRON =================');

        return ;
    }
}

class temperatureCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
	public function dontRemoveCmd() {
        return true;
    }

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->getInformations();
			return;
		}
	}
}
