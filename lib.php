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
 * Adds the ustream item to the course navigation
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 * @return void
 * @throws coding_exception
 * @throws moodle_exception
 */
function local_ustreamseries_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    global $DB, $CFG;

    if (!has_capability('local/ustreamseries:view', $context) || $CFG->version < 2022041900) {
        return;
    }

    $url = new moodle_url('/local/ustreamseries/index.php', array('id' => $course->id));
    $title = get_string('navigationname', 'local_ustreamseries');
    $pix = new pix_icon('play', 'open u:stream', 'block_opencast');
    $newnode = navigation_node::create($title, $url, navigation_node::TYPE_CUSTOM, 'ustreamseries',
        'ustreamseries', $pix);

    $navigation->add_node($newnode);
}


/**
 * This function extends the navigation with the ustream item
 *
 * @param navigation_node $navigation
 */
function local_ustreamseries_extend_navigation($navigation) {
    global $USER, $PAGE, $DB, $CFG;
    if (empty($USER->id) || $CFG->version >= 2022041900) {
        return;
    }

    if ('admin-index' === $PAGE->pagetype) {
        $exists = $DB->record_exists('capabilities', array('name' => 'local/ustreamseries:view'));

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
        $participantsnode = $mycoursesnode->find('participants', navigation_node::TYPE_CONTAINER);
        if ($participantsnode) { // Add the navnode before grades.
            $keys = $participantsnode->parent->get_children_key_list();
            $igrades = array_search('grades', $keys);
            if ($igrades !== false) {
                if (isset($keys[$igrades + 1])) {
                    $beforekey = $keys[$igrades + 1];
                }
            }
        }

        if ($beforekey == null) { // No participants node found, fall back to above sections
            $activitiesnode = $mycoursesnode->find('activitiescategory', navigation_node::TYPE_CATEGORY);
            if ($activitiesnode == false) {
                $sections = $mycoursesnode->find_all_of_type(navigation_node::TYPE_SECTION);
                if (!empty($sections)) {
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



