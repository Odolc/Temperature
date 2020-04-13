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

function temperature_install() {
    jeedom::getApiKey('temperature');

    config::save('functionality::cron5::enable', 1, 'rosee');
    config::save('functionality::cron30::enable', 0, 'rosee');

    $cron = cron::byClassAndFunction('temperature', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    message::add('temperature', 'Merci pour l\'installation du plugin Temperature');
}

function temperature_update() {
    jeedom::getApiKey('temperature');

    $cron = cron::byClassAndFunction('temperature', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    if (config::byKey('functionality::cron5::enable', 'rosee', -1) == -1) {
        config::save('functionality::cron5::enable', 1, 'rosee');
    }

    if (config::byKey('functionality::cron30::enable', 'rosee', -1) == -1) {
        config::save('functionality::cron30::enable', 0, 'rosee');
    }

    $plugin = plugin::byId('temperature');
    $eqLogics = eqLogic::byType($plugin->getId());
    /* foreach ($eqLogics as $eqLogic) {

    }*/

    //resave eqs for new cmd:
    try
    {
        $eqs = eqLogic::byType('temperature');
        foreach ($eqs as $eq){
            $eq->save();
        }
    }
    catch (Exception $e)
    {
        $e = print_r($e, 1);
        log::add('temperature', 'error', 'temperature_update ERROR: '.$e);
    }

    message::add('temperature', 'Merci pour la mise à jour de ce plugin, consultez le changelog');
}

function updateLogicalId($eqLogic, $from, $to) {
    $temperatureCmd = $eqLogic->getCmd(null, $from);
    if (is_object($temperatureCmd)) {
        $temperatureCmd->setLogicalId($to);
        $temperatureCmd->save();
    }
}

function temperature_remove() {
    $cron = cron::byClassAndFunction('temperature', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
?>
