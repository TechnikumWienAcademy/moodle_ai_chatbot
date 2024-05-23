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
 * Plugin upgrade steps are defined here.
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute mod_openaichat upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_openaichat_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024022200) {

        // Define field questionlimit to be added to openaichat.
        $table = new xmldb_table('openaichat');
        $field = new xmldb_field('questionlimit', XMLDB_TYPE_INTEGER, '5', null, null, null, null, 'presence');

        // Conditionally launch add field questionlimit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Openaichat savepoint reached.
        upgrade_mod_savepoint(true, 2024022200, 'openaichat');
    }

    if ($oldversion < 2024051701) {
        // Add openaichat_usertermsofuse table.
        $table = new xmldb_table('openaichat_usertermsofuse');
	$table->setComment('Stores per course module if user has accepted terms of use');

        // Adding fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
	$table->add_field('termsofuseaccepted', XMLDB_TYPE_INTEGER, '1', XMLDB_NOTNULL, null, 0);
        $table->add_field('termsofuseacceptedtime', XMLDB_TYPE_INTEGER, '10', null, null);

        // Adding keys to table assign_overrides.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Create table.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2024051701, 'openaichat');
    }

    return true;
}
