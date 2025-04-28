<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.


//adaption for o1/o3: max_completion_tokens, temperature, top_p kann ENTFERNT Weerden

/**
 * Library of interface functions and constants.
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
//namespace mod_openaichat;

function openaichat_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_openaichat into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_openaichat_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function openaichat_add_instance($moduleinstance, $mform = null) {

    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('openaichat', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_openaichat in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_openaichat_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function openaichat_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('openaichat', $moduleinstance);
}

/**
 * Removes an instance of the mod_openaichat from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function openaichat_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('openaichat', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('openaichat', array('id' => $id));

    return true;
}

//This is for general settings.
function mod_openaichat_get_type_to_display() {
    $stored_type = get_config('mod_openaichat', 'type');
    if ($stored_type) {
        return $stored_type;
    }

    return 'chat';
}

//This is for activity level settings.
function mod_openaichat_get_type_to_display_activity() {
    $stored_type = get_config('mod_openaichat', 'type');
    if ($stored_type) {
        return $stored_type;
    }

    return 'chat';
}

function mod_openaichat_fetch_assistants_array($block_id = null, $modid = null) {
   // global $DB;

   if(!empty($modid)) {
        $apikey = get_mod_config($modid, 'apikey');
   } else {
        $apikey = get_config('mod_openaichat', 'apikey');
   }

    if (!$apikey) {
        return [];
    }

    $curl = new \curl();
    $curl->setopt(array(
        'CURLOPT_HTTPHEADER' => array(
            'Authorization: Bearer ' . $apikey,
            'Content-Type: application/json'
        ),
    ));

    $response = $curl->get("https://api.openai.com/v1/assistants?order=desc");
    $response = json_decode($response);
    $assistant_array = [];
    foreach ($response->data as $assistant) {
        $assistant_array[$assistant->id] = $assistant->name;
    }

    return $assistant_array;
}

function get_ai_models() {
    return [
        "models" => [
            'gpt-4.1' => 'gpt-4.1',
            'gpt-4.1-mini' => 'gpt-4.1-mini',
            'gpt-4.1-nano' => 'gpt-4.1-nano',
            'gpt-4o' => 'gpt-4o',
            'gpt-4o-mini' => 'gpt-4o-mini',
            'o3-mini-2025-01-31' => 'o3-mini-2025-01-31',
            'o3-mini' => 'o3-mini',
            'o1-2024-12-17' => 'o1-2024-12-17',
            'o1' => 'o1',
            'o3-2025-04-16' => 'o3-2025-04-16',
            'gpt-4o-mini-2024-07-18' => 'gpt-4o-mini-2024-07-18',
            'gpt-4o-2024-11-20' => 'gpt-4o-2024-11-20',
            'gpt-4' => 'gpt-4',           
            'gpt-4-1106-preview' => 'gpt-4-1106-preview',
            'gpt-4-0613' => 'gpt-4-0613',
            'gpt-4-0314' => 'gpt-4-0314',
            'gpt-3.5-turbo' => 'gpt-3.5-turbo',
            'gpt-3.5-turbo-16k-0613' => 'gpt-3.5-turbo-16k-0613',
            'gpt-3.5-turbo-16k' => 'gpt-3.5-turbo-16k',
            'gpt-3.5-turbo-1106' => 'gpt-3.5-turbo-1106',
            'gpt-3.5-turbo-0613' => 'gpt-3.5-turbo-0613',
            'gpt-3.5-turbo-0301' => 'gpt-3.5-turbo-0301',
            'gpt-3.5-turbo-0301' => 'gpt-3.5-turbo-0301',
            

        ],
        "types" => [
            'gpt-4.1' => 'chat',
            'gpt-4.1-mini' => 'chat',
            'gpt-4.1-nano' => 'chat',
            'gpt-4o' => 'chat',
            'gpt-4o-mini' => 'chat',
            'o3-mini-2025-01-31' => 'chat',
            'o3-mini' => 'chat',
            'o1-2024-12-17' => 'chat',
            'o1' => 'chat',
            'o3-2025-04-16' => 'chat',
            'gpt-4o-mini-2024-07-18' => 'chat',
            'gpt-4o-2024-11-20' => 'chat',
            'gpt-4' => 'chat',
            'gpt-4-1106-preview' => 'chat',
            'gpt-4-0613' => 'chat',
            'gpt-4-0314' => 'chat',
            'gpt-3.5-turbo' => 'chat',
            'gpt-3.5-turbo-16k-0613' => 'chat',
            'gpt-3.5-turbo-16k' => 'chat',
            'gpt-3.5-turbo-1106' => 'chat',
            'gpt-3.5-turbo-0613' => 'chat',
            'gpt-3.5-turbo-0301' => 'chat',
        ]
    ];
}

//function for getting local activity settings
function get_mod_config($modid, $key = null) {

    global $DB;
    if(!empty($key)) {
        return $DB->get_record('openaichat', array('id' => $modid))->$key;
    }
    return $DB->get_record('openaichat', array('id' => $modid));
}

//function checking if the user has exceeded the question limit
function user_has_questions_left($modid, $userid) {

    global $DB;

    if (get_mod_config($modid, 'questionlimit') == 0) {
        return true;
    }

    $counter = $DB->get_record('openaichat_userlog', array('modid' => $modid, 'userid' => $userid))->questioncounter;

    return ($counter < get_mod_config($modid, 'questionlimit'));
}
