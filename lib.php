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
 * This file contains functions used by the local ustreamseries plugin
 *
 * @package       local_ustreamseries
 * @author        Thomas Wedekind
 * @copyright     2022 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the ustream item
 *
 * @param navigation_node $navigation
 */
function local_ustreamseries_extend_navigation($navigation) {
    global $USER, $PAGE, $DB;
    if (empty($USER->id)) {
        return;
    }

    if ('admin-index' === $PAGE->pagetype) {
        $exists = $DB->record_exists('capabilities', array('name' => 'local/contactlist:view'));

        if (!$exists) {
            return;
        }
    }

    $context = context::instance_by_id($PAGE->context->id);
    $isvalidcontext = ($context instanceof context_course || $context instanceof context_module);
    if (!$isvalidcontext) {
        return;
    }

    $coursecontext = null;
    if ($context instanceof context_module) {
        $coursecontext = $context->get_course_context();
    } else {
        $coursecontext = $context;
    }

    if (!has_capability('local/ustreamseries:view', $coursecontext, $USER)) {
        return;
    }

    $rootnodes = array($navigation->find('mycourses', navigation_node::TYPE_ROOTNODE),
        $navigation->find('courses', navigation_node::TYPE_ROOTNODE));
    foreach ($rootnodes as $mycoursesnode) {
        if (empty($mycoursesnode)) {
            continue;
        }
        $beforekey = null;
        $participantsnode = $mycoursesnode->find('grades', navigation_node::TYPE_CONTAINER);
        if ($participantsnode) { // Add the navnode after participants.
            $keys = $participantsnode->parent->get_children_key_list();
            $igrades = array_search('grades', $keys);
            if ($igrades !== false) {
                if (isset($keys[$igrades + 1])) {
                    $beforekey = $keys[$igrades + 1];
                }
            }
        }

        if ($beforekey == null) { // No participants node found, fall back to other variants!
            $activitiesnode = $mycoursesnode->find('activitiescategory', navigation_node::TYPE_CATEGORY);
            if ($activitiesnode == false) {
                $custom = $mycoursesnode->find_all_of_type(navigation_node::TYPE_CUSTOM);
                $sections = $mycoursesnode->find_all_of_type(navigation_node::TYPE_SECTION);
                if (!empty($custom)) {
                    $first = reset($custom);
                    $beforekey = $first->key;
                } else if (!empty($sections)) {
                    $first = reset($sections);
                    $beforekey = $first->key;
                }
            } else {
                $beforekey = 'activitiescategory';
            }
        }

        $url = new moodle_url('/local/ustreamseries/index.php', array('id' => $coursecontext->instanceid));
        $title = get_string('navigationname', 'local_ustreamseries');
        $childnode = navigation_node::create($title,
            $url,
            navigation_node::TYPE_CUSTOM,
            'ustreamseries',
            'ustreamseries',
            new pix_icon('play', 'open u:stream', 'block_opencast'));
        if (($mycoursesnode !== false && $mycoursesnode->has_children())) {
            $currentcourseinmycourses = $mycoursesnode->find($coursecontext->instanceid, navigation_node::TYPE_COURSE);
            if ($currentcourseinmycourses) {
                $currentcourseinmycourses->add_node($childnode, $beforekey);
                break;
            }
        }
    }
}


/**
 * This function extends the settings navigation block for the course.
 *
 *
 * @param navigation_node $coursenode the node of the course
 * @param object $course the course of the ustream-series
 * @param object $coursecontext the context of the course
 */
function local_ustreamseries_extend_navigation_course($coursenode, $course, $coursecontext) {
    if ($course && $course->id != SITEID) {
        if (has_any_capability(['local/ustreamseries:create',
            'local/ustreamseries:create_lv', 'local/ustreamseries:link',
            'local/ustreamseries:link_other' ], $coursecontext)) {
            $link = new moodle_url('/local/ustreamseries/link_stream.php', ['id' => $course->id]);
            $coursenode->add(get_string('link_stream_settingsmenu',
                'local_ustreamseries'),
                $link, navigation_node::TYPE_SETTING,
                null,
                'ustreamseries',
                new pix_icon('play', 'open u:stream', 'block_opencast'));
        }
    }

}

/**
 * JS for inserting a link to the ustreamseries-dialogue.
 *
 * @return string|void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function local_ustreamseries_before_standard_top_of_body_html() {
    global $PAGE, $COURSE, $OUTPUT;
    $context = context_course::instance($COURSE->id);
    if (!(strpos($PAGE->url->get_path(), '/blocks/opencast/index.php') !== false) || !has_any_capability(
        ['local/ustreamseries:create', 'local/ustreamseries:create_lv',
             'local/ustreamseries:link', 'local/ustreamseries:link_other'],
        $context)) {
        return;
    }
    $templatecontext = [];
    $link = new moodle_url('/local/ustreamseries/link_stream.php', ['id' => $COURSE->id]);
    $templatecontext['url'] = $link->out();
    $templatecontext['title'] = get_string('link_stream_settingsmenu_short', 'local_ustreamseries');
    $templatecontext['text'] = get_string('link_stream_settingsmenu', 'local_ustreamseries');
    $rendered = $OUTPUT->render_from_template('local_ustreamseries/series_link', $templatecontext);
    $PAGE->requires->js_call_amd('local_ustreamseries/settingslink', 'init', [$rendered]);

}

