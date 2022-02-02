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
        global $PAGE, $USER, $COURSE;
        $mform = $this->_form;
        $courseid = $COURSE->id;
        $context = \context_course::instance($courseid);
        $options = [];
        if(\has_capability('local/ustreamseries:create', $context)) {
            $options[LOCAL_USTREAMSERIES_CREATE] = get_string('link_stream_form_create', 'local_ustreamseries');
        }
        if(\has_capability('local/ustreamseries:create', $context)) {
            $options[LOCAL_USTREAMSERIES_CREATE_LV] = get_string('link_stream_form_create_lv', 'local_ustreamseries');
        }
        if(has_capability('local/ustreamseries:link', $context) && local_ustreamseries_is_lv($courseid)) {
            $options[LOCAL_USTREAMSERIES_LINK] = get_string('link_stream_form_link', 'local_ustreamseries');
        }
        if(has_capability('local/ustreamseries:link_other', $context)) {
            $options[LOCAL_USTREAMSERIES_LINK_OTHER] = get_string('link_stream_form_link_other', 'local_ustreamseries');
        }

        $mform->addElement('hidden', 'id', $courseid);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('select', 'action', get_string('link_stream_form_select_action', 'local_ustreamseries'), $options);
        $mform->setType('action', PARAM_ACTION);
        $mform->setDefault('action', LOCAL_USTREAMSERIES_LINK);

        $mform->addElement('text', 'seriesname', get_string('link_stream_form_seriesname', 'local_ustreamseries' ), 'size="20"');
        $mform->setType('seriesname', PARAM_TEXT);
        $mform->hideif('seriesname', 'action', 'eq', LOCAL_USTREAMSERIES_LINK);
        $mform->hideif('seriesname', 'action', 'eq', LOCAL_USTREAMSERIES_LINK_OTHER);

        $mform->addElement('checkbox', 'linkallcourseseries', get_string('link_stream_form_link_all_course_series', 'local_ustreamseries'));
        $mform->hideIf('linkallcourseseries', 'action', 'eq', LOCAL_USTREAMSERIES_LINK_OTHER);
        $mform->hideIf('linkallcourseseries', 'action', 'eq', LOCAL_USTREAMSERIES_CREATE);
        $mform->hideIf('linkallcourseseries', 'action', 'eq', LOCAL_USTREAMSERIES_CREATE_LV);
        
        $mform->setType('linkallcourseseries', PARAM_BOOL);
        $options = local_ustreamseries_get_all_unconnected_course_series($COURSE->id);
        $mform->addElement('select', 'seriesidselect', get_string('link_stream_form_series_id_select', 'local_ustreamseries'), $options);
        $mform->setType('seriesidselect', PARAM_ALPHANUMEXT);
        $mform->hideIf('seriesidselect', 'linkallcourseseries', 'neq', '');
        $mform->hideIf('seriesidselect', 'action', 'eq', LOCAL_USTREAMSERIES_LINK_OTHER);
        $mform->hideIf('seriesidselect', 'action', 'eq', LOCAL_USTREAMSERIES_CREATE_LV);
        $mform->hideIf('seriesidselect', 'action', 'eq', LOCAL_USTREAMSERIES_CREATE);

        $mform->addElement('text', 'seriesid', get_string('link_stream_form_series_id', 'local_ustreamseries'));
        $mform->hideIf('seriesid', 'action', 'eq', LOCAL_USTREAMSERIES_LINK);
        $mform->hideIf('seriesid', 'action', 'eq', LOCAL_USTREAMSERIES_CREATE_LV);
        $mform->hideIf('seriesid', 'action', 'eq', LOCAL_USTREAMSERIES_CREATE);

        $mform->setType('seriesid', PARAM_ALPHANUMEXT);
        $this->add_action_buttons(true);
    }
    
    
    /**
     * Check if everything is correct and check also the user rights for the action;
     * 
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    function validation($data, $files) {
        global $COURSE;
        $errors = [];
        $context = \context_course::instance($COURSE->id);
        switch ($data['action']) {
            case LOCAL_USTREAMSERIES_CREATE:
                require_capability('local/ustreamseries:create', $context);
                break;
            case LOCAL_USTREAMSERIES_CREATE_LV:
                require_capability('local/ustreamseries:create_lv', $context);
                break;
            case LOCAL_USTREAMSERIES_LINK:
                require_capability('local/ustreamseries:link', $context);
                $unconnected = local_ustreamseries_get_all_unconnected_course_series($COURSE->id);
                if(!$unconnected[$data['seriesidselect']]) {
                    $errors['seriesidselect'] = get_string('seriesnotexistsorconnected', 'local_ustreamseries');
                }
                break;
            case LOCAL_USTREAMSERIES_LINK_OTHER:
                require_capability('local/ustreamseries:link_other', $context);
                $connected = local_ustreamseries_get_connected_course_series($COURSE->id);
                if ($connected) {
                    if (array_key_exists($data['seriesid'], $connected)) {
                        $errors['seriesid'] = get_string('link_stream_form_seriesalreadyconnected', 'local_ustreamseries');
                    } else if (!local_ustreamseries_check_series_exists($data['seriesid'])) {
                        $errors['seriesid'] = get_string('link_stream_form_seriesnotexistsorconnected', 'local_ustreamseries');
                    }
                }
                break;
            default:
                return ['action' => get_string('undefined_action', 'local_ustreamseries')];
                break;
        }
    }
}
