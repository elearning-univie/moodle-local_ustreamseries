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
 * Check if block exists and if not send the user to connect their stream.
 *
 * @package    local_ustreamseries
 * @copyright  2022 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// This file is part of mod_offlinequiz for Moodle - http://moodle.org/
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
 * This script lists all the instances of offlinequiz in a particular course
 *
 * @package       local_ustreamseries
 * @author        Thomas Wedekind
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.0
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
require_once("../../config.php");
require_once("locallib.php");

$id = required_param('id', PARAM_INT);
require_login($id);
$coursecontext = context_course::instance($id);
require_capability('local/ustreamseries:view', $coursecontext);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
if (local_ustreamseries_get_connected_course_series($course->id)) {
    $redirecturl = new moodle_url('/blocks/opencast/index.php', array('courseid' => $id));
    redirect($redirecturl);
}
$PAGE->set_url('/local/ustreamseries/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

$PAGE->set_title(get_string('ustream', 'local_ustreamseries'));
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
$warning = get_string('warning_noustream', 'local_ustreamseries', $CFG->wwwroot . '/local/ustreamseries/link_stream.php?id=' . $id);
\core\notification::warning($warning);
echo $OUTPUT->footer();
