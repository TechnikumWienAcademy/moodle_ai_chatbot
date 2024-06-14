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
 * @package    mod_openaichat
 * @subpackage backup-moodle2
 * @copyright  2024 think modular h.khayami@think-modular.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_openaichat_activity_task
 */

/**
 * Define the complete openaichat structure for backup, with file and id annotations
 */
class backup_openaichat_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $openaichat = new backup_nested_element('openaichat', array('id'), array(
            'name', 'timecreated', 'timemodified', 'intro', 'introformat', 'apikey',
            'type', 'assistantname', 'username', 'restrictusage', 'assistant',
            'persistconvo', 'prompt', 'sourceoftruth', 'model', 'temperature', 'maxlength',
            'topp', 'frequency', 'presence', 'questionlimit'));

        // Define sources.
        $openaichat->set_source_table('openaichat', ['id' => backup::VAR_ACTIVITYID]);

        // Define file annotations.
        $openaichat->annotate_files('mod_openaichat', 'intro', null);

        // Return the root element (openaichat), wrapped into standard activity structure.
        return $this->prepare_activity_structure($openaichat);
    }
}
