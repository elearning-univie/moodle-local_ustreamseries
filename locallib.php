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
 * This is the locallib for local_ustreamseries.
 * @package       local_ustreamseries
 * @author        Thomas Wedekind
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.11
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


use block_opencast\local\apibridge;
use tool_opencast\local\api;

define('LOCAL_USTREAMSERIES_CREATE','create');
define('LOCAL_USTREAMSERIES_CREATE_LV','createlv');
define('LOCAL_USTREAMSERIES_LINK','link');
define('LOCAL_USTREAMSERIES_LINK_OTHER','linkother');

/**
 *
 * @package       local_ustreamseries
 * @author        Jakob Mischke
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.11
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Connect an opencast series with a moodle course.
 * 
 * Die aufgerufere Funktion import_series_to_course_with_acl_change() tut folgendes:
 * - In Opencast werden der Serie die ACLs hinzugefügt, die in den Adminsettings gelistet sind. 
 * - Dann wird die Serie mit dem Moodlekurs vernüpft. 
 * - Sie wird als Defaultserie gesetzt, wenn es noch keine Defaultserie gibt.
 * @param int $courseid Moodle Kurs ID
 * @param string $ocseriesid Opencast Serien-ID
 */
function local_ustreamseries_connect($courseid, $ocseriesid) {
    global $USER;

    $apibridge = apibridge::get_instance(); // Get default instance.

    $result = $apibridge->import_series_to_course_with_acl_change($courseid, $ocseriesid, $USER->id);

    if ($result->error == 1) {
        return false; // TODO: Exception
    } else {
        return true;
    }
}

/**
 * Get an array of series connected to this moodle course in i3v, but still not connected with the course.
 * 
 * @param int $courseid Moodle Kurs ID
 * @return array Return an array with seriesid -> seriestitle mappings
 */
function local_ustreamseries_get_all_unconnected_course_series($courseid) {
    //der einzige Kurs, für den die Abfrage funktioniert ist moodletest:117733 <-> cbf059ac-3a67-46f0-9e22-9e7ad43d9faa <-> SS2021-850002-1

    //$courseid = 117733; // Debuggin hack for local instance.

    $api = api::get_instance();

    $series = $api->oc_get('/v1/campus/univie/series/byMoodleCourseId/' . $courseid);

    $response = json_decode($series);

    if ($response) {
        $connectedSeries = local_ustreamseries_get_connected_course_series_array($courseid);
        $result = array ();
        foreach ($response as $singleseries) {
            if (!in_array($singleseries->seriesId, $connectedSeries)) {
                    $result[$singleseries->seriesId] = $singleseries->title;
            }
        }
        if ($result) {
            return $result;
        }
    } 
    return null;
}

/**
 * Get all Opencast series, already imported into this course.
 * 
 * @param int $courseid Moodle Kurs ID
 * @return array $result seriesid -> seriestitle
 */
function local_ustreamseries_get_connected_course_series($courseid) {

    $apibridge = apibridge::get_instance();
    $series = $apibridge->get_course_series($courseid);
    $result = [];
    if ($series) {

        foreach ($series as $singleseries) {
            $ocseries = $apibridge->get_series_by_identifier($singleseries->series);
            $result[$singleseries->series] = $ocseries->title;
        }
    }
    
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


/**
 * Get a simple array, containing all Opencast series IDs, already imported into this course.
 * 
 * @param int $courseid Moodle Kurs ID
 * @return array $seriestitles
 */
function local_ustreamseries_get_connected_course_series_array($courseid) {
    $apibridge = apibridge::get_instance();
    
    $series = $apibridge->get_course_series($courseid);
    if ($series) {
        $result = array ();
        foreach ($series as $singleseries) {
            $result[] = $singleseries->series;
        }
    }
    
    if ($result) {
        return $result;
    } else {
        return null;
    }
    
}


function local_ustreamseries_create_course_series($courseid, $name) {
    //TODO
}

function local_ustreamseries_check_series_exists($seriesid) {
    //TODO
    return true;
}

function local_ustreamseries_is_lv($courseid) {
    global $DB;
    $result = $DB->get_field('course', 'shortname', ['id' => $courseid]);
    return preg_match('/^\d{4}[WS] \d*-\d* .*/', $result);
}

 /**
 * Check, whether a moodle user has write permission to a series in Opencast.
 * 
 * Checks Opencast writing permission for the u:account connected to the given moodle-user.
 * @param string $ocseriesid Opncast seriesid to check permissions for
 * @param int $userid moodle user-ID to check permissions for. Should be the u:account
 * @return bool $result true/false
 */
function local_ustreamseries_check_user_edit_permission($ocseriesid, $userid = null) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }
    
    $api = api::get_instance();

    $response = $api->oc_get('/api/series/' . $ocseriesid . '/acl');

    $seriesacls = json_decode($response);

    $userisallowed = false;

    $thisrole = local_ustreamseries_muid_to_ocrole($userid);
    foreach ($seriesacls as $seriesacl) {
        if ($seriesacl->role == $thisrole && $seriesacl->allow == true && $seriesacl->action == 'write') {
            $userisallowed = true;
        }
    }

    return $userisallowed;
    
}

 /**
 * Convert a Moodle User ID to the respecting Opencast personal role of that user' u:account
 * 
 * @param int $userid user ID in moodle
 * @return string $role opencast role for the connected u:account
 */
function local_ustreamseries_muid_to_ocrole($userid = null) {
    global $USER;

    if (!$userid) {
        $username = $USER->username;
    } else {
        $username = core_user::get_user($userid)->username;
    }

    return 'ROLE_USER_' . strtoupper($username);
}