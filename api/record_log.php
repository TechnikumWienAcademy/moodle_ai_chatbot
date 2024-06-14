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

global $DB, $USER;

$modid = required_param('modId', PARAM_INT);
$requestmessage = required_param('requestMessage', PARAM_TEXT);
$responsemessage = required_param('responseMessage', PARAM_TEXT);
$sesskey = sesskey();

$logdata = [
    'modid' => $modid,
    'sesskey' => $sesskey,
    'request' => $requestmessage,
    'response' => $responsemessage,
];

if (!empty($modid) && !empty($requestmessage) && !empty($responsemessage) && !empty($sesskey)) {

    $DB->insert_record('openaichat_chatlog', $logdata);

    if(!$DB->record_exists('openaichat_userlog', ['modid' => $modid, 'userid' => $USER->id])) {
        $DB->insert_record('openaichat_userlog', ['modid' => $modid, 'userid' => $USER->id, 'questioncounter' => 1]);
    } else {
        $record = $DB->get_record('openaichat_userlog', ['modid' => $modid, 'userid' => $USER->id]);
        $record->questioncounter += 1;
        $DB->update_record('openaichat_userlog',$record);
    }
}