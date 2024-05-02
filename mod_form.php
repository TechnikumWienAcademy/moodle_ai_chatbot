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

/**
 * The main mod_openaichat configuration form.
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot .'/mod/openaichat/lib.php');

/**
 * Module instance settings form.
 *
 * @package     mod_openaichat
 * @copyright   2023 think modular
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_openaichat_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $PAGE;

        $modid = $PAGE->cm->instance;
        $cmid = $PAGE->cm->id;

        $PAGE->requires->js_call_amd('mod_openaichat/mod_settings', 'init', [[
            'allowinstancesettings' => get_config('mod_openaichat', 'allowinstancesettings'),
            'modid' => $modid
        ]]);

        $assistant_array = mod_openaichat_fetch_assistants_array(null, $modid);

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('openaichatname', 'mod_openaichat'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        $mform->addElement('html', html_writer::link("$CFG->wwwroot/mod/openaichat/report.php?cmid=$cmid&modid=$modid", "Click here to see the log report."));

        $mform->addElement('header', 'openaichatsettings', get_string('pluginname', 'mod_openaichat'));

        // Adding the openai API key.
        $mform->addElement('password', 'apikey', get_string('apikey', 'mod_openaichat'), array('size' => '120'));
        $mform->setDefault('apikey', get_config('mod_openaichat', 'apikey'));
        $mform->addHelpButton('apikey', 'apikey', 'mod_openaichat');

        // Adding the type.
        $types = [
            "chat" => "chat",
            "assistant" => "assistant",
        ];
        $mform->addElement('select', 'type', get_string('type', 'mod_openaichat'), $types);
        $mform->setDefault('type', get_config('mod_openaichat', 'type'));
        $mform->addHelpButton('type', 'type', 'mod_openaichat');

        //It's not logical to have this part in local activity settings.
        // $mform->addElement('advcheckbox', 'restrictusage', get_string('restrictusage', 'mod_openaichat'));
        // $mform->setDefault('restrictusage', get_config('mod_openaichat', 'restrictusage'));
        // $mform->addHelpButton('restrictusage', 'restrictusage', 'mod_openaichat');

        $mform->addElement('text', 'questionlimit', get_string('questionlimit', 'mod_openaichat'), array('size' => '4'));
        $mform->setDefault('questionlimit', get_config('mod_openaichat', 'questionlimit'));
        $mform->addHelpButton('questionlimit', 'questionlimit', 'mod_openaichat');

        // Adding the "assistant name" field.
        $mform->addElement('text', 'assistantname', get_string('assistantname', 'mod_openaichat'), array('size' => '30'));
        $mform->setDefault('assistantname', get_config('mod_openaichat', 'assistantname'));
        $mform->addHelpButton('assistantname', 'assistantname', 'mod_openaichat');

        // Adding the "user name" field.
        $mform->addElement('text', 'username', get_string('username', 'mod_openaichat'), array('size' => '30'));
        $mform->setDefault('username', get_config('mod_openaichat', 'username'));
        $mform->addHelpButton('username', 'username', 'mod_openaichat');

        $mform->addElement('header', 'assistantheading', get_string('assistantheading', 'mod_openaichat'));
        $mform->addHelpButton('assistantheading', 'assistantheading', 'mod_openaichat');

        if (count($assistant_array)) {
            $mform->addElement('select', 'assistant', get_string('assistant', 'mod_openaichat'), $assistant_array);
            $firstoption = count($assistant_array) ? reset($assistant_array) : null;
            $mform->setDefault('assistant', $firstoption);
            $mform->addHelpButton('assistant', 'assistant', 'mod_openaichat');
        } else {
            $mform->addElement('static', 'assistant', get_string('assistant', 'mod_openaichat'), get_string('noassistants', 'mod_openaichat'));
        }

        $mform->addElement('advcheckbox', 'persistconvo', get_string('persistconvo', 'mod_openaichat'));
        $mform->setDefault('persistconvo', get_config('mod_openaichat', 'persistconvo'));
        $mform->addHelpButton('persistconvo', 'persistconvo', 'mod_openaichat');

        $mform->addElement('header', 'chatheading', get_string('chatheading', 'mod_openaichat'));
        $mform->addHelpButton('chatheading', 'chatheading', 'mod_openaichat');

        $mform->addElement('textarea', 'prompt', get_string('prompt', 'mod_openaichat'), 'wrap="virtual" rows="10" cols="80"');
        $mform->setDefault('prompt', get_config('mod_openaichat', 'prompt'));
        $mform->addHelpButton('prompt', 'prompt', 'mod_openaichat');

        $mform->addElement('textarea', 'sourceoftruth', get_string('sourceoftruth', 'mod_openaichat'), 'wrap="virtual" rows="10" cols="80"');
        $mform->setDefault('sourceoftruth', get_config('mod_openaichat', 'sourceoftruth'));
        $mform->addHelpButton('sourceoftruth', 'sourceoftruth', 'mod_openaichat');

        $mform->addElement('header', 'advanced', get_string('advanced', 'mod_openaichat'));
        $mform->addHelpButton('advanced', 'advanced', 'mod_openaichat');

        $mform->addElement('select', 'model', get_string('model', 'mod_openaichat'), get_ai_models()['models']);
        $mform->setDefault('model', get_config('mod_openaichat', 'model'));
        $mform->addHelpButton('model', 'model', 'mod_openaichat');

        $mform->addElement('text', 'temperature', get_string('temperature', 'mod_openaichat'), array('size' => '6'));
        $mform->setDefault('temperature', get_config('mod_openaichat', 'temperature'));
        $mform->addHelpButton('temperature', 'temperature', 'mod_openaichat');

        $mform->addElement('text', 'maxlength', get_string('maxlength', 'mod_openaichat'), array('size' => '6'));
        $mform->setDefault('maxlength', get_config('mod_openaichat', 'maxlength'));
        $mform->addHelpButton('maxlength', 'maxlength', 'mod_openaichat');

        $mform->addElement('text', 'topp', get_string('topp', 'mod_openaichat'), array('size' => '6'));
        $mform->setDefault('topp', get_config('mod_openaichat', 'topp'));
        $mform->addHelpButton('topp', 'topp', 'mod_openaichat');

        $mform->addElement('text', 'frequency', get_string('frequency', 'mod_openaichat'), array('size' => '6'));
        $mform->setDefault('frequency', get_config('mod_openaichat', 'frequency'));
        $mform->addHelpButton('frequency', 'frequency', 'mod_openaichat');

        $mform->addElement('text', 'presence', get_string('presence', 'mod_openaichat'), array('size' => '6'));
        $mform->setDefault('presence', get_config('mod_openaichat', 'presence'));
        $mform->addHelpButton('presence', 'presence', 'mod_openaichat');

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
