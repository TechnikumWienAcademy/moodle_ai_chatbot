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

require_once('../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/openaichat/lib.php');

echo '<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
      <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.min.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.0/css/buttons.dataTables.min.css">
      <script src="https://cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.html5.min.js"></script>
      ';

global $COURSE;
$cmid = optional_param('cmid', null, PARAM_INT);
$modid = optional_param('modid', null, PARAM_INT);
$context = context_module::instance($cmid);

if (!is_siteadmin()) {
    if (!has_capability('mod/openaichat:seeopenailog', $context)) {
        exit;
    }
}

require_login();

global $DB;

if(empty($modid)) {
    $data_arr = $DB->get_records('openaichat_chatlog');
} else {
    $data_arr = $DB->get_records('openaichat_chatlog', ['modid' => $modid]);
}

echo $OUTPUT->header();
$table = new html_table();
$table->head = array('order', 'Session id', 'Activity', 'Questions', 'Answers');
$order = 1;
foreach($data_arr as $record) {
    $activityname = $DB->get_record('openaichat', ['id' => $record->modid])->name;
    $activityurl = '<a href="' . $CFG->wwwroot . '/mod/openaichat/view.php?id=' . $cmid . '">' . $activityname . '</a>';
    $table->data[] = array($order, $record->sesskey, $activityurl, $record->request, $record->response);
    $order += 1;
}
echo html_writer::table($table);

echo "<script>let table = new DataTable('.generaltable', {
    dom: 'Bfrtip',
    buttons: [
        'csv'
    ]
});
console.log(table);</script>
";

echo $OUTPUT->footer();