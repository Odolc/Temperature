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

    public static function cron5() {
    //public static function cron15() {
        foreach (eqLogic::byType('temperature') as $temperature) {
			log::add('temperature', 'debug', 'pull cron');
			$temperature->getInformations();
	}
    }


    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {

    	if ($this->getConfiguration('temperature') == '') {
    		throw new Exception(__('Le champ temperature ne peut etre vide',__FILE__));
	}

	if ($this->getConfiguration('humidite') == '') {
        	throw new Exception(__('Le champ humidite ne peut etre vide',__FILE__));
    	}
    	
    	if ($this->getConfiguration('vent') == '') {
        	throw new Exception(__('Le champ vitesse du vent ne peut etre vide',__FILE__));
    	}
    	
    }

    public function postInsert() {
    	// Ajout d'une commande dans le tableau pour le windchill
        $windchillCmd = new temperatureCmd();
        $windchillCmd->setName(__('Windchill', __FILE__));
        $windchillCmd->setEqLogic_id($this->id);
	$windchillCmd->setLogicalId('windchill');
        $windchillCmd->setConfiguration('data', 'windchill');
        $windchillCmd->setType('info');
        $windchillCmd->setSubType('numeric');
        $windchillCmd->setUnite('°C');
        $windchillCmd->setEventOnly(1);
	$windchillCmd->setIsHistorized(0);
	$windchillCmd->setDisplay('generic_type','GENERIC');
        $windchillCmd->save();

        // Ajout d'une commande dans le tableau pour l'indice de chaleur
        $indiceChaleurCmd = new temperatureCmd();
        $indiceChaleurCmd->setName(__('Indice de chaleur', __FILE__));
        $indiceChaleurCmd->setEqLogic_id($this->id);
	$indiceChaleurCmd->setLogicalId('IndiceChaleur');
        $indiceChaleurCmd->setConfiguration('data', 'indiceChaleur');
        $indiceChaleurCmd->setType('info');
        $indiceChaleurCmd->setSubType('numeric');
        $indiceChaleurCmd->setUnite('°C');
        $indiceChaleurCmd->setEventOnly(1);
	$indiceChaleurCmd->setIsHistorized(0);
	//$indiceChaleurCmd->setIsVisible(0);
	$indiceChaleurCmd->setDisplay('generic_type','GENERIC');
        $indiceChaleurCmd->save();

	// Ajout d'une commande dans le tableau pour l'alerte inconfort
        $AlertInconfortCmd = new temperatureCmd();
        $AlertInconfortCmd->setName(__('Alerte inconfort', __FILE__));
        $AlertInconfortCmd->setEqLogic_id($this->id);
	$AlertInconfortCmd->setLogicalId('alerte_inconfort');
        $AlertInconfortCmd->setConfiguration('data', 'alert_h');
        $AlertInconfortCmd->setType('info');
        $AlertInconfortCmd->setSubType('binary');
        $AlertInconfortCmd->setUnite('');
        $AlertInconfortCmd->setEventOnly(1);
	$AlertInconfortCmd->setIsHistorized(0);
	//$AlertInconfortCmd->setIsVisible(1);
	$AlertInconfortCmd->setDisplay('generic_type','GENERIC');
        $AlertInconfortCmd->save();
        
	// Ajout d'une commande dans le tableau pour l'info inconfort 
        $InfoInconfortCmd = new temperatureCmd();
        $InfoInconfortCmd->setName(__('Message inconfort', __FILE__));
        $InfoInconfortCmd->setEqLogic_id($this->id);
	$InfoInconfortCmd->setLogicalId('info_inconfort');
        $InfoInconfortCmd->setConfiguration('data', 'info_inconfort');
        $InfoInconfortCmd->setType('info');
        $InfoInconfortCmd->setSubType('string');
        $InfoInconfortCmd->setUnite('');
        $InfoInconfortCmd->setEventOnly(1);
	$InfoInconfortCmd->setIsHistorized(0);
	//$InfoInconfortCmd->setIsVisible(1);
	$InfoInconfortCmd->setDisplay('generic_type','GENERIC');
        $InfoInconfortCmd->save();

	
	}

	/*  **********************Getteur Setteur*************************** */
	public function postUpdate() {
        foreach (eqLogic::byType('temperature') as $temperature) {
            	$temperature->getInformations();
		}
	}

	public function getInformations() {
	$idvirt = str_replace("#","",$this->getConfiguration('temperature'));
	$cmdvirt = cmd::byId($idvirt);
	if (is_object($cmdvirt)) {
		$temperature = $cmdvirt->execCmd();
		log::add('temperature', 'debug', 'Configuration : temperature ' . $temperature);
	} else {
		log::add('temperature', 'error', 'Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
	}

	$idvirt = str_replace("#","",$this->getConfiguration('humidite'));
	$cmdvirt = cmd::byId($idvirt);
	if (is_object($cmdvirt)) {
		$humidite = $cmdvirt->execCmd();
		log::add('temperature', 'debug', 'Configuration : humidite ' . $humidite);
	} else {
		log::add('temperature', 'error', 'Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
	}
	
	$idvirt = str_replace("#","",$this->getConfiguration('vent'));
	$cmdvirt = cmd::byId($idvirt);
	if (is_object($cmdvirt)) {
		$vent = $cmdvirt->execCmd();
		log::add('temperature', 'debug', 'Configuration : vent ' . $vent);
	} else {
		log::add('temperature', 'error', 'Configuration : vent non existant : ' . $this->getConfiguration('vent'));
	}
		
	$seuil=$this->getConfiguration('SEUIL');
	if ($seuil == '') {
		//valeur par défaut du seuil d'alerte d'inconfort pour l'indice de chaleur
		$seuil=40;
	}   
	log::add('temperature', 'debug', 'Configuration : seuil Inconfort ' . $seuil);

	
	// valeurs pour test, l'indice de chaleur doit être de 53°C...
	/*
	$temperature = 35.0;
	log::add('temperature', 'debug', 'temperature ' . $temperature);
	$humidite = 90.0;
	log::add('temperature', 'debug', 'humidite ' . $humidite);
	*/

	/* calcul du windchill
		
  	*/

  	if($temperature > 10.0) {
  		$windchill = $temperature;
  		$visibleWindchill = 0;
  	} else {
  		if($vent >= 4.8) {
  			$Terme1 = 13.12 + 0.6215 * $temperature;
  			$Terme2 = 0.3965 * $temperature - 11.37;
  			$Terme3 = pow($vent,0.16);
  			$windchill = $Terme2 * $Terme3 + $Terme1;
  		} else {
  			$windchill = $temperature + 0.2 * (0.1345 * $temperature - 1.59) * $vent;
		}
		$visibleWindchill = 1;
	}
  	$windchill = round(($windchill), 1);
	log::add('temperature', 'debug', 'Windchill ' . $windchill);
	log::add('temperature', 'debug', 'visibleWindchill ' . $visibleWindchill);
        //log::add('windchill', 'info', 'getInformations');

  	/* calcul de l'indice de chaleur
  		
  	*/

	if(($temperature > 27.0) and ($humidite > 40.0)) {
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
		log::add('temperature', 'debug', 'tempF ' . $tempF);
		$terme1 = $c1 + $c2 * $tempF + $c3 * $humidite + $c4 * $tempF * $humidite;
		$terme2 = $c5 * pow($tempF,2.0);
		$terme3 = $c6 * pow($humidite,2.0);
		$terme4 = $c7 * $humidite * pow($tempF,2.0);
		$terme5 = $c8 * $tempF * pow($humidite,2.0);
		$terme6 = $c9 * pow($tempF,2.0) * pow($humidite,2.0);
		$indiceChaleurF = $terme1 + $terme2 + $terme3 + $terme4 + $terme5 + $terme6;
		$indiceChaleur = ($indiceChaleurF - 32.0) / 1.8;
		$indiceChaleur = round(($indiceChaleur), 1);
		if($indiceChaleur < 33.0) {
			$info_inconfort = 'inconfort';
		}
		if(($indiceChaleur >= 33.0) and ($indiceChaleur < 40.0)) {
			$info_inconfort = 'extrême inconfort';
		}
		if(($indiceChaleur >= 40.0) and ($indiceChaleur < 51.0)) {
			$info_inconfort = 'danger';
		}
		if($indiceChaleur >= 51.0) {
			$info_inconfort = 'danger extrême';
		}		
		
  		$visibleIndice = 1;
  	} else {
  		$visibleIndice = 0;
		$indiceChaleur = '';
  	}
	log::add('temperature', 'debug', 'indiceChaleur ' . $indiceChaleur);
	log::add('temperature', 'debug', 'visibleIndice ' . $visibleIndice);
	log::add('temperature', 'debug', 'info_inconfort ' . $info_inconfort);
	
	// Calcul de l'alerte inconfort indice de chaleur en fonction du seuil d'alerte
	if ($visibleIndice == 1) {
		if(($indiceChaleur) >= $seuil) {
			$alert_h = 1;
		} else {
			$alert_h = 0;
		}
	} else {
		$alert_h = 0;
	}
	
	foreach ($this->getCmd() as $cmd) {
		if($cmd->getConfiguration('data')=="windchill"){
			$cmd->setConfiguration('value', $windchill);
			$cmd->setIsVisible($visibleWindchill);
			$cmd->save();
			$cmd->event($windchill);
			//log::add('temperature', 'debug', 'Windchill ' . $windchill);
		}
		
		if($cmd->getConfiguration('data')=="info_inconfort"){
			$cmd->setConfiguration('value', $info_inconfort);
			$cmd->setIsVisible($visibleIndice);
			$cmd->save();
			$cmd->event($info_inconfort);
			//log::add('temerature', 'debug', 'info_inconfort ' . $info_inconfort);
		}

		if($cmd->getConfiguration('data')=="indiceChaleur"){
			$cmd->setConfiguration('value', $indiceChaleur);
			$cmd->setIsVisible($visibleIndice);
			$cmd->save();
			$cmd->event($indiceChaleur);
			//log::add('temerature', 'debug', 'indiceChaleur ' . $indiceChaleur);
		}
				
		if($cmd->getConfiguration('data')=="alert_h"){
			$old_alert_h = $cmd->execCmd();
			log::add('temperature', 'debug', 'old_alert_h ' . $old_alert_h);
			$cmd->setConfiguration('value', $alert_h);
			$cmd->save();
			if ($alert_h!=$old_alert_h) {
				$cmd->setCollectDate('');
				$cmd->event($alert_h);
			}
			log::add('temperature', 'debug', 'alert_h ' . $alert_h);
		}
	}
        return ;
    }
}

class temperatureCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
	public function execute($_options = null) {
	}

}

?>
