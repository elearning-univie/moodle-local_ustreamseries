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
 * @package       local_ustreamseries
 * @author        Thomas Wedekind
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.11
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
require_once("../../config.php");
require_once("locallib.php");

global $PAGE, $OUTPUT, $DB;

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

$blockurl = new \moodle_url('/blocks/opencast/index.php', array('courseid' => $id));
$blockstring = get_string('link_to_block', 'local_ustreamseries');

$mform = new \local_ustreamseries\form\link_stream_form();

if ($mform->is_cancelled()) {
    $redirecturl = new moodle_url('/course/view.php', array('id' => $id));
    redirect($redirecturl);
}

$formdata = $mform->get_data();
if ($formdata) {
    if ($formdata->action == LOCAL_USTREAMSERIES_CREATE) {

        if ($formdata->createselect == LOCAL_USTREAMSERIES_CREATE_PERSONAL) {
            $result = local_ustreamseries_create_personal_series($id, $formdata->seriesname);
            if ($result) {
                $successmessage = array('title' => $result->seriestitle, 'link' => $blockurl->out());
                \core\notification::info(get_string('series_creation_success', 'local_ustreamseries', $successmessage));
            }
            $redirecturl = new moodle_url('/local/ustreamseries/link_stream.php',['id' => $id]);
            redirect($redirecturl);
        } else {
            $result = local_ustreamseries_create_lv_series($id, $formdata->createselect);
            if ($result) {
                $successmessage = array('title' => $result->seriestitle, 'link' => $blockurl->out());
                \core\notification::info(get_string('series_creation_success', 'local_ustreamseries', $successmessage));
            }
            $redirecturl = new moodle_url('/local/ustreamseries/link_stream.php',['id' => $id]);
            redirect($redirecturl);
        }
    } else if ($formdata->action == LOCAL_USTREAMSERIES_LINK) {
        $result = null;
        if ($formdata->linkallcourseseries) {
            foreach (local_ustreamseries_get_all_unconnected_course_series($id) as $courseserieskey => $courseseriesvalue) {
                $result = null;
                $result = local_ustreamseries_connect($id, $courseserieskey);
                if ($result) {
                    $successmessage = array('title' => $result->seriestitle, 'link' => $blockurl->out());
                    \core\notification::info(get_string('series_link_success', 'local_ustreamseries', $successmessage));
                }
            }
        } else if ($formdata->linkselect == LOCAL_USTREAMSERIES_LINK_OTHER) { // A series from text field.
            $result = local_ustreamseries_connect($id, $formdata->seriesid);
            if ($result) {
                $successmessage = array('title' => $result->seriestitle, 'link' => $blockurl->out());
                \core\notification::info(get_string('series_link_success', 'local_ustreamseries', $successmessage));
            } else {
                $formdata->seriesid = '';
                $mform->set_data($formdata);
            }
        } else { // A LV series from the list.
            $result = local_ustreamseries_connect($id, $formdata->linkselect);
            if ($result) {
                $successmessage = array('title' => $result->seriestitle, 'link' => $blockurl->out());
                \core\notification::info(get_string('series_link_success', 'local_ustreamseries', $successmessage));
            }
        }
    }
}

$series = $DB->get_records('tool_opencast_series', array('courseid' => $id));
// Transform isdefault to int.
array_walk($series, function ($item) {
    $item->isdefault = intval($item->isdefault);
});

if ($series) {
    $ocinstanceid = 1;
    $templatecontext = new stdClass();
    $templatecontext->addseriesallowed = count($series) < get_config('block_opencast', 'maxseries_' . $ocinstanceid);

    $params = [
        $coursecontext->id,
        $ocinstanceid,
        0,
        array_values($series),
        get_config('block_opencast', 'maxseries_' . $ocinstanceid)
    ];

    $PAGE->requires->js_call_amd('block_opencast/block_manage_series', 'init', $params);
    $PAGE->requires->css('/blocks/opencast/css/tabulator.min.css');
    $PAGE->requires->css('/blocks/opencast/css/tabulator_bootstrap4.min.css');
}
echo $OUTPUT->header();

echo "<a href=\"$blockurl\" id=\"local_ustream_link_to_block\"
      class=\"d-flex flex-wrap flex-row-reverse m-b-1 text-right\">$blockstring</a>";
if ($series) {
    echo $OUTPUT->heading(get_string('editexistingseries', 'local_ustreamseries'));
    echo $OUTPUT->render_from_template('local_ustreamseries/series_table', $templatecontext);
}

echo $OUTPUT->heading(get_string('addnewseries', 'local_ustreamseries'));
$notificationtype = \core\output\notification::NOTIFY_WARNING;
echo $OUTPUT->notification(get_string('series_editable', 'local_ustreamseries'), $notificationtype);
$mform->display();
echo $OUTPUT->footer();
