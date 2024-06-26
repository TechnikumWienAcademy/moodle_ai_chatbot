<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Class providing completions for assistant API
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/openaichat/lib.php');

if (get_config('mod_openaichat', 'restrictusage') !== "0") {
    require_login();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

global $DB;

$modid = required_param('modId', PARAM_INT);
$userid = required_param('userId', PARAM_TEXT);

$questionlimit = get_mod_config($modid, 'questionlimit');

$counter = $DB->get_record('openaichat_userlog', ['modid' => $modid, 'userid' => $USER->id])->questioncounter;

if ($questionlimit == 0) {
    echo "-1";
} else {
    if ($counter < $questionlimit) {
        //remaining questions
        echo $questionlimit - $counter;
    } else {
        echo "false";
    }
}