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
 * Plugin settings
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot .'/mod/openaichat/lib.php');
$type = mod_openaichat_get_type_to_display();
$assistant_array = mod_openaichat_fetch_assistants_array();

global $PAGE, $ADMIN;

$PAGE->requires->js_call_amd('mod_openaichat/settings', 'init');

$ADMIN->add('reports', new admin_externalpage('mod_openaichat_reportlog', get_string('openailog', 'mod_openaichat'), "$CFG->wwwroot/mod/openaichat/report.php", 'report/log:view'));

$settings->add(new \admin_setting_configtext(
    'mod_openaichat/apikey',
    get_string('apikey', 'mod_openaichat'),
    get_string('apikeydesc', 'mod_openaichat'),
    '',
    PARAM_TEXT
));

$settings->add(new \admin_setting_configselect(
    'mod_openaichat/type',
    get_string('type', 'mod_openaichat'),
    get_string('typedesc', 'mod_openaichat'),
    'chat',
    ['chat' => 'chat', 'assistant' => 'assistant']
));

$settings->add(new \admin_setting_configcheckbox(
    'mod_openaichat/restrictusage',
    get_string('restrictusage', 'mod_openaichat'),
    get_string('restrictusagedesc', 'mod_openaichat'),
    1
));

$settings->add(new \admin_setting_configtext(
    'mod_openaichat/assistantname',
    get_string('assistantname', 'mod_openaichat'),
    get_string('assistantnamedesc', 'mod_openaichat'),
    'Assistant',
    PARAM_TEXT
));

$settings->add(new \admin_setting_configtext(
    'mod_openaichat/username',
    get_string('username', 'mod_openaichat'),
    get_string('usernamedesc', 'mod_openaichat'),
    'User',
    PARAM_TEXT
));

$settings->add(new \admin_setting_configtext(
    'mod_openaichat/questionlimit',
    get_string('questionlimit', 'mod_openaichat'),
    get_string('questionlimitdesc', 'mod_openaichat'),
    '',
    PARAM_TEXT
));

// Assistant settings //

if ($type === 'assistant') {
    $settings->add(new \admin_setting_heading(
        'mod_openaichat/assistantheading',
        get_string('assistantheading', 'mod_openaichat'),
        get_string('assistantheadingdesc', 'mod_openaichat')
    ));

    if (count($assistant_array)) {
        $settings->add(new \admin_setting_configselect(
            'mod_openaichat/assistant',
            get_string('assistant', 'mod_openaichat'),
            get_string('assistantdesc', 'mod_openaichat'),
            count($assistant_array) ? reset($assistant_array) : null,
            $assistant_array
        ));
    } else {
        $settings->add(new \admin_setting_description(
            'mod_openaichat/noassistants',
            get_string('assistant', 'mod_openaichat'),
            get_string('noassistants', 'mod_openaichat'),
        ));
    }

    $settings->add(new \admin_setting_configcheckbox(
        'mod_openaichat/persistconvo',
        get_string('persistconvo', 'mod_openaichat'),
        get_string('persistconvodesc', 'mod_openaichat'),
        1
    ));

} else {

    // Chat settings //

    $settings->add(new \admin_setting_heading(
        'mod_openaichat/chatheading',
        get_string('chatheading', 'mod_openaichat'),
        get_string('chatheadingdesc', 'mod_openaichat')
    ));

    $settings->add(new \admin_setting_configtextarea(
        'mod_openaichat/prompt',
        get_string('prompt', 'mod_openaichat'),
        get_string('promptdesc', 'mod_openaichat'),
        "Below is a conversation between a user and a support assistant for a Moodle site, where users go for online learning.",
        PARAM_TEXT
    ));

    $settings->add(new \admin_setting_configtextarea(
        'mod_openaichat/sourceoftruth',
        get_string('sourceoftruth', 'mod_openaichat'),
        get_string('sourceoftruthdesc', 'mod_openaichat'),
        '',
        PARAM_TEXT
    ));
}


// Advanced Settings //

$settings->add(new \admin_setting_heading(
    'mod_openaichat/advanced',
    get_string('advanced', 'mod_openaichat'),
    get_string('advanceddesc', 'mod_openaichat')
));

$settings->add(new \admin_setting_configcheckbox(
    'mod_openaichat/allowinstancesettings',
    get_string('allowinstancesettings', 'mod_openaichat'),
    get_string('allowinstancesettingsdesc', 'mod_openaichat'),
    0
));

if ($type === 'assistant') {

} else {
    $settings->add(new \admin_setting_configselect(
        'mod_openaichat/model',
        get_string('model', 'mod_openaichat'),
        get_string('modeldesc', 'mod_openaichat'),
        'text-davinci-003',
        get_ai_models()['models']
    ));

    $settings->add(new \admin_setting_configtext(
        'mod_openaichat/temperature',
        get_string('temperature', 'mod_openaichat'),
        get_string('temperaturedesc', 'mod_openaichat'),
        0.5,
        PARAM_FLOAT
    ));

    $settings->add(new \admin_setting_configtext(
        'mod_openaichat/maxlength',
        get_string('maxlength', 'mod_openaichat'),
        get_string('maxlengthdesc', 'mod_openaichat'),
        500,
        PARAM_INT
    ));

    $settings->add(new \admin_setting_configtext(
        'mod_openaichat/topp',
        get_string('topp', 'mod_openaichat'),
        get_string('toppdesc', 'mod_openaichat'),
        1,
        PARAM_FLOAT
    ));

    $settings->add(new \admin_setting_configtext(
        'mod_openaichat/frequency',
        get_string('frequency', 'mod_openaichat'),
        get_string('frequencydesc', 'mod_openaichat'),
        1,
        PARAM_FLOAT
    ));

    $settings->add(new \admin_setting_configtext(
        'mod_openaichat/presence',
        get_string('presence', 'mod_openaichat'),
        get_string('presencedesc', 'mod_openaichat'),
        1,
        PARAM_FLOAT
    ));
}