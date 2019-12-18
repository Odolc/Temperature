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
		foreach (eqLogic::byType('temperature') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('temperature', 'debug', '================= CRON 5 ==================');
				$rosee->getInformations();
			}
		}
	}
    
  	public static function cron30($_eqlogic_id = null) {
		//no both cron5 and cron30 enabled:
		if (config::byKey('functionality::cron5::enable', 'temperature', 0) == 1)
		{
			config::save('functionality::cron30::enable', 0, 'temperature');
			return;
		}
		foreach (eqLogic::byType('temperature') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('temperature', 'debug', '================= CRON 30 =================');
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
            $windchillCmd->setIsHistorized(0);
            $windchillCmd->setDisplay('generic_type','GENERIC_INFO');
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
            $indiceChaleurCmd->setIsHistorized(0);
            $indiceChaleurCmd->setIsVisible(0);
            $indiceChaleurCmd->setDisplay('generic_type','GENERIC_INFO');
            $indiceChaleurCmd->save();

	// Ajout d'une commande dans le tableau pour l'alerte inconfort
            $AlertInconfortCmd = new temperatureCmd();
            $AlertInconfortCmd->setName(__('Alerte Humidex', __FILE__));
            $AlertInconfortCmd->setEqLogic_id($this->id);
            $AlertInconfortCmd->setLogicalId('alerte_humidex');
            $AlertInconfortCmd->setConfiguration('data', 'alert_h');
            $AlertInconfortCmd->setType('info');
            $AlertInconfortCmd->setSubType('binary');
            $AlertInconfortCmd->setUnite('');
            $AlertInconfortCmd->setIsHistorized(0);
            $AlertInconfortCmd->setDisplay('generic_type','GENERIC_INFO');
            $AlertInconfortCmd->save();
        
	// Ajout d'une commande dans le tableau pour la  pré-alerte inconfort
            $AlertInconfortCmd = new temperatureCmd();
            $AlertInconfortCmd->setName(__('Pré Alerte Humidex', __FILE__));
            $AlertInconfortCmd->setEqLogic_id($this->id);
            $AlertInconfortCmd->setLogicalId('palerte_humidex');
            $AlertInconfortCmd->setConfiguration('data', 'alert_ph');
            $AlertInconfortCmd->setType('info');
            $AlertInconfortCmd->setSubType('binary');
            $AlertInconfortCmd->setUnite('');
            $AlertInconfortCmd->setIsHistorized(0);
            $AlertInconfortCmd->setDisplay('generic_type','GENERIC_INFO');
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
            $InfoInconfortCmd->setIsHistorized(0);
            $InfoInconfortCmd->setIsVisible(0);
            $InfoInconfortCmd->setDisplay('generic_type','GENERIC_INFO');
            $InfoInconfortCmd->save();
	}

  	public function postSave()
    {
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
		log::add('temperature', 'debug', '================= CONFIGURATION : ' .$_eqName.' =================');
        /*  ********************** TEMPERATURE *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('temperature'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $temperature = $cmdvirt->execCmd();
                log::add('temperature', 'debug', 'Temperature : ' . $temperature.'°C');
            } else {
                log::add('temperature', 'error', 'Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
            }
        
        /*  ********************** HUMIDITE *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('humidite'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $humidite = $cmdvirt->execCmd();
                log::add('temperature', 'debug', 'Humidite : ' . $humidite.'%');
            } else {
                log::add('temperature', 'error', 'Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
            }
        
        /*  ********************** VENT *************************** */        
            $idvirt = str_replace("#","",$this->getConfiguration('vent'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $vent = $cmdvirt->execCmd();
                log::add('temperature', 'debug', 'Vent : ' . $vent.' ');
            } else {
                log::add('temperature', 'error', 'Configuration : vent non existant : ' . $this->getConfiguration('vent'));
            }
        
		/*  ********************** Seuil Alerte Humidex*************************** */ 
            $seuil=$this->getConfiguration('SEUIL');
            if ($seuil == '') {
                $seuil=40;
                log::add('temperature', 'debug', 'Seuil Alerte Humidex : Aucune valeur de saisie');
                log::add('temperature', 'debug', 'Seuil Alerte Humidex par défaut : ' . $seuil);
            } else {
                log::add('temperature', 'debug', 'Seuil Alerte Humidex : ' . $seuil);
            }
        
		/*  ********************** Seuil Alerte Humidex*************************** */ 
            $pre_seuil=$this->getConfiguration('PRE_SEUIL');
            if ($pre_seuil == '') {
                $pre_seuil=30;
                log::add('temperature', 'debug', 'Seuil Pré-Alerte Humidex Aucune valeur de saisie');
                log::add('temperature', 'debug', 'Seuil Pré-Alerte Humidex par défaut : ' . $pre_seuil);
            } else {
                log::add('temperature', 'debug', 'Seuil Pré-alerte Humidex : ' . $pre_seuil);
            }

	/* calcul du windchill
		
  	*/
        log::add('temperature', 'debug', '========= CALCUL DU WINDCHILL ========');
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
            log::add('temperature', 'debug', 'Windchill : ' . $windchill.'°C');
            log::add('temperature', 'debug', 'visibleWindchill : ' . $visibleWindchill);
        //log::add('windchill', 'info', 'getInformations');

  	/* calcul de l'indice de chaleur
  		
  	*/
        log::add('temperature', 'debug', '========= CALCUL DU FACTEUR HUMIDEX ========');
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
		      //log::add('temperature', 'debug', 'tempF : ' . $tempF);
            $terme1 = $c1 + $c2 * $tempF + $c3 * $humidite + $c4 * $tempF * $humidite;
            $terme2 = $c5 * pow($tempF,2.0);
            $terme3 = $c6 * pow($humidite,2.0);
            $terme4 = $c7 * $humidite * pow($tempF,2.0);
            $terme5 = $c8 * $tempF * pow($humidite,2.0);
            $terme6 = $c9 * pow($tempF,2.0) * pow($humidite,2.0);
            $indiceChaleurF = $terme1 + $terme2 + $terme3 + $terme4 + $terme5 + $terme6;
            $indiceChaleur = ($indiceChaleurF - 32.0) / 1.8;
            $indiceChaleur = round(($indiceChaleur), 1);
                log::add('temperature', 'debug', 'Indice de Chaleur (Humidex) ' . $indiceChaleur.'°C');
            if($indiceChaleur < 15.0) {
                $info_inconfort = 'Sensation de frais ou de froid';
            }elseif(($indiceChaleur >= 15.0) and ($indiceChaleur <= 19.0)) {
                $info_inconfort = 'Aucun inconfort';
            }elseif(($indiceChaleur > 19.0) and ($indiceChaleur <= 29.0)) {
                $info_inconfort = "Sensation de bien être";
            }elseif(($indiceChaleur > 29.0) and ($indiceChaleur <= 34.0)) {
                $info_inconfort = "Sensation d'inconfort plus ou moins grande";
            }elseif(($indiceChaleur > 34.0) and ($indiceChaleur <= 39.0)) {
                $info_inconfort = "Sensation d'inconfort assez grande. Prudence. Ralentir certaines activités en plein air.";
            }elseif(($indiceChaleur > 39.0) and ($indiceChaleur <= 45.0)) {
                $info_inconfort = "Sensation d'inconfort généralisée. Danger. Éviter les efforts.";
            }elseif(($indiceChaleur > 45.0) and ($indiceChaleur <= 53.0)) {
                $info_inconfort = 'Danger extrême. Arrêt de travail dans de nombreux domaines.';
            }else {
                $info_inconfort = 'Coup de chaleur imminent (danger de mort).';
            }    
	
	// Calcul de l'alerte inconfort indice de chaleur en fonction du seuil d'alerte
        log::add('temperature', 'debug', '=============== ALERTE HUMIDEX ===============');
            if(($indiceChaleur) >= $seuil) {
                $alert_h = 1;
            } else {
                $alert_h = 0;
            }
            if(($indiceChaleur) >= $pre_seuil) {
                $alert_ph = 1;
            } else {
                $alert_ph = 0;
            }
        log::add('temperature', 'debug', 'Seuil Pré-alerte Humidex : ' . $alert_ph);
        log::add('temperature', 'debug', 'Seuil Alerte Haute Humidex : ' . $alert_h);
        log::add('temperature', 'debug', '=============== MISE A JOUR ===============');
        
        $cmd = $this->getCmd('info', 'windchill');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $windchill);
			$cmd->save();
            $cmd->event($windchill);
                log::add('temperature', 'debug', 'Windchill : ' . $windchill.'°C');
		}
     
        $cmd = $this->getCmd('info', 'info_inconfort');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $info_inconfort);
			$cmd->save();
            $cmd->event($info_inconfort);
                log::add('temperature', 'debug', 'Degré de comfort : ' . $info_inconfort. '');
		}
  
        $cmd = $this->getCmd('info', 'indiceChaleur');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $indiceChaleur);
			$cmd->save();
            $cmd->event($indiceChaleur);
                log::add('temperature', 'debug', 'Facteur Humidex : ' . $indiceChaleur.'°C');
		}
 
        $cmd = $this->getCmd('info', 'alert_h');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_h);
			$cmd->save();
            $cmd->event($alert_h);
                log::add('temperature', 'debug', 'Facteur Humidex : ' . $alert_h.'°C');
		}        
        
        $cmd = $this->getCmd('info', 'alerte_humidex');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_h);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($alert_h);
                log::add('temperature', 'debug', 'Etat Alerte Haute Humidex : ' . $alert_h. '');
		}
        
        $cmd = $this->getCmd('info', 'palerte_humidex');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_ph);
			$cmd->save();
            $cmd->setCollectDate('');
            $cmd->event($alert_ph);
                log::add('temperature', 'debug', 'Etat Pré-alerte Humidex : ' . $alert_ph. '');
		}
        
        return ;
    }
}

class temperatureCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
	public function dontRemoveCmd()
    {
        return true;
    }

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->getInformations();
			return;
		}
	}
}

?>