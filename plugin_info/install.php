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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function temperature_install()
{
    jeedom::getApiKey('temperature');

    config::save('functionality::cron5::enable', 0, 'temperature');
    config::save('functionality::cron10::enable', 0, 'temperature');
    config::save('functionality::cron15::enable', 1, 'temperature');
    config::save('functionality::cron30::enable', 0, 'temperature');
    config::save('functionality::cronhourly::enable', 0, 'temperature');

    $cron = cron::byClassAndFunction('temperature', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    //message::add('Plugin Température', 'Merci pour l\'installation du plugin.');
}

function temperature_update()
{
    jeedom::getApiKey('temperature');

    $cron = cron::byClassAndFunction('temperature', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    if (config::byKey('functionality::cron5::enable', 'temperature', -1) == -1) {
        config::save('functionality::cron5::enable', 1, 'temperature');
    }

    if (config::byKey('functionality::cron10::enable', 'temperature', -1) == -1) {
        config::save('functionality::cron10::enable', 1, 'temperature');
    }

    if (config::byKey('functionality::cron15::enable', 'temperature', -1) == -1) {
        config::save('functionality::cron15::enable', 1, 'temperature');
    }

    if (config::byKey('functionality::cron30::enable', 'temperature', -1) == -1) {
        config::save('functionality::cron30::enable', 0, 'temperature');
    }

    if (config::byKey('functionality::cronHourly::enable', 'temperature', -1) == -1) {
        config::save('functionality::cronHourly::enable', 0, 'temperature');
    }

    $plugin = plugin::byId('temperature');
    $eqLogics = eqLogic::byType($plugin->getId());
    foreach ($eqLogics as $eqLogic) {
        //updateLogicalId($eqLogic, 'alert_1',null,null);
        updateLogicalId($eqLogic, 'IndiceChaleur', 'heat_index', 1);
        updateLogicalId($eqLogic, 'alerte_humidex', 'alert_2', null);
        updateLogicalId($eqLogic, 'info_inconfort', 'td', null);
        updateLogicalId($eqLogic, 'palerte_humidex', 'alert_1', null);

        updateLogicalId($eqLogic, 'heat_index', null, 1);
        updateLogicalId($eqLogic, 'windchill', null, 1);
    }

    //resave eqs for new cmd:
    try {
        $eqs = eqLogic::byType('temperature');
        foreach ($eqs as $eq) {
            $eq->save();
        }
    } catch (Exception $e) {
        $e = print_r($e, 1);
        log::add('temperature', 'error', 'temperature_update ERROR: ' . $e);
    }

    //message::add('Plugin Température', 'Merci pour la mise à jour de ce plugin, consultez le changelog.');
    foreach (eqLogic::byType('temperature') as $temperature) {
        $temperature->getInformations();
    }
}

function updateLogicalId($eqLogic, $from, $to, $_historizeRound = null)
{
    $command = $eqLogic->getCmd(null, $from);
    if (is_object($command)) {
        if ($to != null) {
            $command->setLogicalId($to);
        }
        if ($_historizeRound != null) {
            $command->setConfiguration('historizeRound', $_historizeRound);
        }
        $command->save();
    }
}

function temperature_remove()
{
    $cron = cron::byClassAndFunction('temperature', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
