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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>

<script>
    $(function() {
        $('#div_confPlugin > .panel.panel-primary').hide()
    })

    $("input[data-l1key='functionality::cron5::enable']").on('change', function() {
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron10::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron15::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron30::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cronHourly::enable']").prop("checked", false)
    });

    $("input[data-l1key='functionality::cron10::enable']").on('change', function() {
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron5::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron15::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron30::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cronHourly::enable']").prop("checked", false)
    });

    $("input[data-l1key='functionality::cron15::enable']").on('change', function() {
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron5::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron10::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron30::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cronHourly::enable']").prop("checked", false)
    });

    $("input[data-l1key='functionality::cron30::enable']").on('change', function() {
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron5::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron10::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron15::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cronHourly::enable']").prop("checked", false)
    });

    $("input[data-l1key='functionality::cronHourly::enable']").on('change', function() {
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron5::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron10::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron15::enable']").prop("checked", false)
        if ($(this).is(':checked')) $("input[data-l1key='functionality::cron30::enable']").prop("checked", false)
    });
</script>