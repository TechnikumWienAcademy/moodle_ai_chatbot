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
 * Module class
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mod_openaichat {

    public function render()
    {
        $c = $this->get_content();
        return '<div class="mod_openaichat"><p id="remaining-questions"></p>'.$c->text.$c->footer.'</div>';
    }

    private function get_content() {

        global $PAGE, $USER;

        $modid = $PAGE->cm->instance;

        // Send data to front end
        $persistconvo = get_mod_config($modid, "persistconvo");

        $PAGE->requires->js_call_amd('mod_openaichat/lib', 'init', [[
            'modId' => $modid,
            'api_type' => get_mod_config($modid, "type"),
            'persistConvo' => $persistconvo,
            'userId' => $USER->id,
        ]]);

        // First, fetch the global settings for these (and the defaults if not set)
        $assistantname = get_mod_config($modid, "assistantname") ? get_mod_config($modid, "assistantname") : get_config('mod_openaichat', 'assistantname');
        $username = get_mod_config($modid, "username") ? get_mod_config($modid, "username") : get_config('mod_openaichat', 'username');
        $assistantname = format_string($assistantname, true, ['context' => $this->context]);
        $username = format_string($username, true, ['context' => $this->context]);

        $this->content = new stdClass;
        $this->content->text = '
            <script>
                var assistantName = "' . $assistantname . '";
                var userName = "' . $username . '";
            </script>

            <style>
                ' . $showlabelscss . '
                .openai_message.user:before {
                    content: "' . $username . '";
                }
                .openai_message.bot:before {
                    content: "' . $assistantname . '";
                }
            </style>

            <div id="openai_chat_log" role="log"></div>
        ';

        //$this->content->footer = get_config('mod_openaichat', 'apikey') ? '
        $this->content->footer = get_mod_config($modid, "apikey") ? '
            <div id="control_bar">
                <div id="input_bar">
                    <textarea id="openai_input" placeholder="' . get_string('askaquestion', 'mod_openaichat') .'" type="text" name="message" rows="4" cols="50"" /></textarea>
                    <button title="Submit" id="go"><i class="fa fa-arrow-right"></i></button>
                </div>
                <button title="New chat" id="refresh"><i class="fa fa-refresh"></i></button>
            </div>'
        : get_string('apikeymissing', 'mod_openaichat');

        return $this->content;
    }
}
