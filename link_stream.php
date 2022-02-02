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
 * Connect new ustream series with 
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
 * @package       local
 * @subpackage    ustreamseries
 * @author        Thomas Wedekind
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.11
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
require_once("../../config.php");
require_once("locallib.php");

$id = required_param('id', PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);
require_login($id);
$coursecontext = context_course::instance($id);
require_capability('local/ustreamseries:view', $coursecontext);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

$PAGE->set_url('/local/ustreamseries/link_stream.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');
$PAGE->set_course($course);
$PAGE->set_context($coursecontext);
$PAGE->set_title(get_string('link_ustream', 'local_ustreamseries'));
$PAGE->set_heading($course->fullname);

$mform = new \local_ustreamseries\form\link_stream_form();
$formdata = $mform->get_data();
if ($formdata) {
    if($mform->is_cancelled()) {
        //TODO send user to course page;
    }
    if($formdata->action == LOCAL_USTREAMSERIES_CREATE) {
        $result = local_ustreamseries_create_series($id, false, $formdata->seriesname);
        if($result) {
            \core\notification::error(get_string('series_creation_failed', 'local_ustreamseries'));
        } else {
            \core\notification::info(get_string('series_creation_success', 'local_ustreamseries'));
        }
    }
    else if($formdata->action == LOCAL_USTREAMSERIES_CREATE_LV) {
        $result = local_ustreamseries_create_series($id, true, $formdata->seriesname);
        if($result) {
            \core\notification::error(get_string('series_creation_failed', 'local_ustreamseries'));
        } else {
            \core\notification::info(get_string('series_creation_success', 'local_ustreamseries'));
        }
    } else if($formdata->action == LOCAL_USTREAMSERIES_LINK) {
        $result = local_ustreamseries_connect($id, $formdata->seriesidselect);
        if($result) {
            \core\notification::info(get_string('series_link_success', 'local_ustreamseries'));
        } else {
            \core\notification::error(get_string('series_link_failed', 'local_ustreamseries'));            
        }
    } else if($action == LOCAL_USTREAMSERIES_LINK_OTHER) {
        $result = local_ustreamseries_connect($id, $formdata->seriesid);
        if($result) {
            \core\notification::info(get_string('series_link_success', 'local_ustreamseries'));
        } else {
            \core\notification::error(get_string('series_link_failed', 'local_ustreamseries'));
        }
    }
}
\core\notification::warning(get_string('series_editable', 'local_ustreamseries'));
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
