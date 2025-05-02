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
 * API endpoint for retrieving assistants
 *
 * @package    mod_openaichat
 * @copyright  2024 think modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_openaichat;

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/openaichat/lib.php');


global $DB, $PAGE;

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

$apikey = required_param('apikey', PARAM_TEXT);

if (!$apikey) {
    print_r([]);
}

$curl = new \curl();
$curl->setopt(array(
    'CURLOPT_HTTPHEADER' => array(
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json',
        'OpenAI-Beta: assistants=v2'
    ),
));

$response = $curl->get("https://api.openai.com/v1/assistants?order=desc");

echo $response;
