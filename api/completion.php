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
 * API endpoint for retrieving GPT completion
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_openaichat;

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/openaichat/lib.php');
require_once($CFG->dirroot . '/mod/openaichat/classes/completion.php');
require_once($CFG->dirroot . '/mod/openaichat/classes/completion/chat.php');

global $DB, $PAGE;

if (get_config('mod_openaichat', 'restrictusage') !== "0") {
    require_login();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

$body = json_decode(file_get_contents('php://input'), true);
$message = clean_param($body['message'], PARAM_NOTAGS);
$history = clean_param_array($body['history'], PARAM_NOTAGS, true);
$modId = clean_param($body['modId'], PARAM_INT, true);
$thread_id = clean_param($body['threadId'], PARAM_NOTAGS, true);

$mod_settings = get_mod_config($modId); //object
$mod_settings_array = [];


$setting_names = [
    'sourceoftruth',
    'prompt',
    'username',
    'assistantname',
    'apikey',
    'model',
    'temperature',
    'maxlength',
    'topp',
    'frequency',
    'presence',
    'assistant'
];

foreach($setting_names as $setting){
    if($mod_settings && property_exists($mod_settings, $setting)) {
        $mod_settings_array[$setting] = $mod_settings->$setting;
    } else {
        $mod_settings_array[$setting] = "";
    }
}

$mod_settings_array['modid'] = $modId;
$engine_class;
$model = $mod_settings->model;
$api_type = $mod_settings->type;

if ($api_type === 'assistant') {
    $engine_class = '\mod_openaichat\completion\assistant';
} else {
    $engines = get_ai_models()['types'];
    if (get_config('mod_openaichat', 'allowinstancesettings') === "1" && $model) {
        $model = $model;
    }
    if (!$model) {
        $model = 'gpt-3.5-turbo';
    }
    $engine_class = '\mod_openaichat\completion\\' . $engines[$model];
}
$completion = new $engine_class(...[$model, $message, $history, $mod_settings_array, $thread_id]);
$response = $completion->create_completion($PAGE->context);

$response["message"] = format_text($response["message"], FORMAT_MARKDOWN, ['context' => $context]);
$response = json_encode($response);

echo $response;