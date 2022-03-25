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
 * ustre
 *
 * @package    local_ustreamseries
 * @author     Thomas Wedekind
 * @copyright  2022 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ustreamseries\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/local/ustreamseries/locallib.php');

/**
 * Form to link and create new ustream series
 *
 * @package    local_ustreamseries
 * @author     Thomas Wedekind
 * @copyright  2022 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link_stream_form extends \moodleform {

    /**
     * Form definition method.
     */
    public function definition() {
        global $COURSE, $PAGE;
        $mform = $this->_form;
        $courseid = $COURSE->id;
        $context = \context_course::instance($courseid);

        $islv = local_ustreamseries_is_lv($courseid);
        if ($islv) {
            $unconnseries = local_ustreamseries_get_all_unconnected_course_series($COURSE->id);
            $possiblelvseries = local_ustreamseries_get_possible_course_series($COURSE->id);
        } else {
            $unconnseries = [];
            $possiblelvseries = [];
        }

        $createoptions = [];
        if (has_capability('local/ustreamseries:create_lv', $context) && $possiblelvseries ) {
            $createoptions = $possiblelvseries;
        }
        if (has_capability('local/ustreamseries:create_personal', $context)) {
            $createoptions[LOCAL_USTREAMSERIES_CREATE_PERSONAL] = get_string('link_stream_form_create_personal', 'local_ustreamseries');
        }
        $linkoptions = [];
        if (has_capability('local/ustreamseries:link_lv', $context)) {
            $linkoptions = $unconnseries;
        }
        if (has_capability('local/ustreamseries:link_other', $context)) {
            $linkoptions[LOCAL_USTREAMSERIES_LINK_OTHER] = get_string('link_stream_form_link_other', 'local_ustreamseries');
        }
        $actionoptions = [];
        if ($createoptions) {
            $actionoptions[LOCAL_USTREAMSERIES_CREATE] = get_string('link_stream_form_create', 'local_ustreamseries');
        }
        if ($linkoptions) {
            $actionoptions[LOCAL_USTREAMSERIES_LINK] = get_string('link_stream_form_link', 'local_ustreamseries');
        }
        if (!$actionoptions) {
            $mform->addElement('text', 'nooptionsavailable', get_string('link_stream_form_nooptionsavailable', 'local_ustreamseries'));
            return;
        }

        $mform->addElement('hidden', 'id', $courseid);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('select', 'action', get_string('link_stream_form_select_action', 'local_ustreamseries'), $actionoptions);
        $mform->setType('action', PARAM_ACTION);
        $mform->setDefault('action', LOCAL_USTREAMSERIES_LINK);
        $mform->addHelpButton('action', 'link_stream_form_select_action', 'local_ustreamseries');

        // First we start with the create part of the form.
        if (count($createoptions) > 1) {
            $mform->addElement('select', 'createselect',
                get_string('link_stream_form_createselect', 'local_ustreamseries'), $createoptions);
            $mform->setType('createselect', PARAM_ALPHANUMEXT);
            $mform->hideIf('createselect', 'action', 'neq', LOCAL_USTREAMSERIES_CREATE);
            $mform->addHelpButton('createselect', 'link_stream_form_createselect', 'local_ustreamseries');
            $mform->setDefault('createselect', array_key_first($createoptions));
        } else if ($createoptions) {
            $mform->addElement('hidden', 'createselect', array_key_first($createoptions));
            $mform->setType('createselect', PARAM_ALPHANUMEXT);
        }
        if ($createoptions) {
            $mform->addElement('text', 'seriesname',
                get_string('link_stream_form_seriesname', 'local_ustreamseries' ),
                'size="20"');
            $mform->setType('seriesname', PARAM_TEXT);
            $mform->hideif('seriesname', 'action', 'neq', LOCAL_USTREAMSERIES_CREATE);

            $mform->hideIf('seriesname', 'createselect', 'neq', LOCAL_USTREAMSERIES_CREATE_PERSONAL);
        }

        if ($linkoptions) {
            if (count($unconnseries) > 1) {
                // Now we go on with the link part of the form
                $mform->addElement('checkbox', 'linkallcourseseries',
                    get_string('link_stream_form_link_all_course_series', 'local_ustreamseries'));
                $mform->setType('linkallcourseseries', PARAM_BOOL);
                $mform->hideIf('linkallcourseseries', 'action', 'neq', LOCAL_USTREAMSERIES_LINK);
            }
            if (count($linkoptions) > 1) {
                $mform->addElement('select', 'linkselect',
                    get_string('link_stream_form_linkselect', 'local_ustreamseries'), $linkoptions);
                $mform->setType('linkselect', PARAM_ALPHANUMEXT);
                $mform->hideIf('linkselect', 'action', 'neq', LOCAL_USTREAMSERIES_LINK);
                $mform->hideIf('linkselect', 'linkallcourseseries', 'eq', 'checked');
            } else {
                $mform->addElement('hidden', 'linkselect', array_key_first($linkoptions));
                $mform->setType('linkselect', PARAM_ALPHANUMEXT);
            }
        }
        if (has_capability('local/ustreamseries:link_other', $context)) {
            $mform->addElement('text', 'seriesid', get_string('link_stream_form_series_id', 'local_ustreamseries'));
            $mform->setType('seriesid', PARAM_ALPHANUMEXT);
            $mform->addHelpButton('seriesid', 'link_stream_form_series_id', 'local_ustreamseries');
            $mform->hideIf('seriesid', 'action', 'neq', LOCAL_USTREAMSERIES_LINK);
            if ($unconnseries) {
                $mform->hideIf('seriesid', 'linkallcourseseries', 'eq', 'checked');
                $mform->hideIf('seriesid', 'linkselect', 'neq', LOCAL_USTREAMSERIES_LINK_OTHER);
            }
        }
        $PAGE->requires->js_call_amd('local_ustreamseries/link_stream_form_headings', 'init');
        $this->add_action_buttons(true, get_string('runbutton', 'local_ustreamseries'));

    }

    /**
     * Check if everything is correct and check also the user rights for the action;
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        global $COURSE;
        $errors = [];
        $context = \context_course::instance($COURSE->id);
        switch ($data['action']) {
            case LOCAL_USTREAMSERIES_CREATE:
                if ($data['createselect'] == LOCAL_USTREAMSERIES_CREATE_PERSONAL) {
                    require_capability('local/ustreamseries:create_personal', $context);
                    if (!$data['seriesname'] || $data['seriesname'] == "") {
                        $errors['seriesname'] = get_string('error_no_seriesname', 'local_ustreamseries');
                    }
                } else {
                    require_capability('local/ustreamseries:create_lv', $context);
                    $result = array_key_exists($data['createselect'], local_ustreamseries_get_possible_course_series($COURSE->id));
                    if (!$result) {
                        $errors['createselect'] = get_string('link_stream_form_series_canntot_be_created', 'local_ustreamseries');
                    }
                }
                break;
            case LOCAL_USTREAMSERIES_LINK:
                if (array_key_exists('linkselect', $data) && $data['linkselect'] != LOCAL_USTREAMSERIES_LINK_OTHER) {
                    // Unconnected series from the list.
                    require_capability('local/ustreamseries:link_lv', $context);
                    $unconnected = local_ustreamseries_get_all_unconnected_course_series($COURSE->id);
                    if (!$unconnected[$data['linkselect']]) {
                        $connected = local_ustreamseries_get_connected_course_series($COURSE->id);
                        if (!$connected[$data['linkselect']]) {
                            $errors['linkselect'] = get_string('link_stream_form_seriesnotexists', 'local_ustreamseries');
                        } else {
                            $errors['linkselect'] = get_string('link_stream_form_seriesalreadyconnected', 'local_ustreamseries');
                        }
                    }
                } else if (array_key_exists('linkselect', $data) && $data['linkselect'] == LOCAL_USTREAMSERIES_LINK_OTHER) {
                    // Link from the text field.
                    require_capability('local/ustreamseries:link_other', $context);
                    if (!$data['seriesid'] || $data['seriesid'] == "") {
                        $errors['seriesid'] = get_string('error_no_seriesid', 'local_ustreamseries');
                    }
                    $connected = local_ustreamseries_get_connected_course_series($COURSE->id);
                    if ($connected) {
                        if (array_key_exists($data['seriesid'], $connected)) {
                            $errors['seriesid'] = get_string('link_stream_form_seriesalreadyconnected', 'local_ustreamseries');
                        } else if (!local_ustreamseries_check_series_exists($data['seriesid'])) {
                            $errors['seriesid'] = get_string('link_stream_form_seriesnotexists', 'local_ustreamseries');
                        }
                    }
                }
                break;
            default:
                $errors['action'] = get_string('undefined_action', 'local_ustreamseries');
                break;
        }
        return $errors;
    }
}
