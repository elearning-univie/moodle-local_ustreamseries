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

define('LOCAL_USTREAMSERIES_CREATE', 'create');
define('LOCAL_USTREAMSERIES_CREATE_PERSONAL', 'createpersonal');
define('LOCAL_USTREAMSERIES_CREATE_LV', 'createlv');
define('LOCAL_USTREAMSERIES_LINK', 'link');
define('LOCAL_USTREAMSERIES_LINK_LV', 'linklv');
define('LOCAL_USTREAMSERIES_LINK_OTHER', 'linkother');

/**
 *
 * @package       local_ustreamseries
 * @author        Jakob Mischke
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.11
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get an array of series connected to this moodle course in i3v, but still not connected with the course.
 *
 * @param int $courseid Moodle Kurs ID
 * @return array|null Return an array with seriesid -> seriestitle mappings or ['emptyseries' => 'Leere Serie'] or null
 */
function local_ustreamseries_get_all_unconnected_course_series($courseid) {

    $api = api::get_instance();

    $response = $api->oc_get('/v1/campus/univie/series/byMoodleCourseId/' . $courseid);

    if ($api->get_http_code() == 200) {
        $series = json_decode($response);
    } else if ($api->get_http_code() == 500) {
        \core\notification::error(get_string('error_coursenotfound', 'local_ustreamseries', $courseid));
    } else {
        \core\notification::error(get_string('error_reachustream', 'local_ustreamseries').$response);
    }

    if ($series) {
        $connectedseries = local_ustreamseries_get_connected_course_series($courseid);
        $result = array ();
        foreach ($series as $singleseries) {
            if ($connectedseries) {
                if (!array_key_exists($singleseries->seriesId, $connectedseries)) {
                        $result[$singleseries->seriesId] = $singleseries->title;
                }
            } else {
                $result[$singleseries->seriesId] = $singleseries->title;
            }
        }
        if ($result) {
            return $result;
        }
    }
    return [];
}

/**
 * Get all possible lv-series that are not yet created in opencast
 *
 * @param int $courseid Moodle Kurs ID
 * @return array|null Return an array with keys: 'TID_Term' and values: Title of series
 */
function local_ustreamseries_get_possible_course_series($courseid = null) {

    $api = api::get_instance();

    try {
        $response = $api->oc_get('/v1/campus/univie/lva/byMoodleCourseId/' . $courseid.'?withoutSeries=true');
    } catch (Exception $e) {
        \core\notification::error(get_string('error_reachustream', 'local_ustreamseries').$response);
        return null;
    }

    if ($api->get_http_code() == 200) {
        $series = json_decode($response);
    } else if ($api->get_http_code() == 500) {
        \core\notification::error(get_string('error_coursenotfound', 'local_ustreamseries', $courseid));
    } else {
        \core\notification::error(get_string('error_reachustream', 'local_ustreamseries').$response);
    }

    if ($series) {
        $result = array ();
        foreach ($series as $singleseries) {
            $singleid = str_replace('.', '-', $singleseries->id);
            $resultkey = $singleid.'_'.$singleseries->term;
            $result[$resultkey] = $singleseries->title;
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
 * @return array $result seriesid -> seriestitle or null
 */
function local_ustreamseries_get_connected_course_series($courseid) {

    $apibridge = apibridge::get_instance();
    $series = $apibridge->get_course_series($courseid);
    $result = [];
    if ($series) {

        foreach ($series as $singleseries) {
            $ocseries = $apibridge->get_series_by_identifier($singleseries->series);
            if ($ocseries != null) {
                $result[$singleseries->series] = $ocseries->title;
            } else {
                \core\notification::error(get_string('error_no_series_found', 'local_ustreamseries', $singleseries->series));
            }
        }
    }

    if ($result) {
        return $result;
    } else {
        return null;
    }
}

/**
 * Connect an opencast series with a moodle course.
 *
 * Die aufgerufere Funktion import_series_to_course_with_acl_change() tut folgendes:
 * - In Opencast werden der Serie die ACLs hinzugefügt, die in den Adminsettings gelistet sind.
 * - Dann wird die Serie mit dem Moodlekurs vernüpft.
 * - Sie wird als Defaultserie gesetzt, wenn es noch keine Defaultserie gibt.
 * @param int $courseid Moodle Kurs ID
 * @param string $ocseriesid Opencast Serien-ID
 * @throws \exception
 * @return object if succesfull or null if it failed
 */
function local_ustreamseries_connect($courseid, $ocseriesid) {
    global $USER;

    $apibridge = apibridge::get_instance(); // Get default instance.

    $result = null;

    if (local_ustreamseries_check_user_edit_permission($ocseriesid, $USER->id)) {
        $result = $apibridge->import_series_to_course_with_acl_change($courseid, $ocseriesid, $USER->id);
    } else {
        return null;
    }

    if ($result && $ocseriesid == $result->seriesid) {
        if ($result->error == 1) {
            \core\notification::error(get_string('error_connectseries', 'local_ustreamseries', $ocseriesid));
            return null;
        } else {
            $result->seriestitle = $apibridge->get_series_by_identifier($ocseriesid)->title;
            return $result;
        }
    } else {
        \core\notification::error(get_string('error_connectseries', 'local_ustreamseries', $ocseriesid));
        return null;
    }
}

/**
 * Creates a series in u:stream and connects it with moodle
 * @param int $courseid the id of the course for the series
 * @param string $name the name of the series. If null, take from block-settings
 * @return object $result true/false
 */
function local_ustreamseries_create_personal_series($courseid, $name = null) {
    global $USER;

    $apibridge = apibridge::get_instance();

    $params = [];
    if ($name) {
        $params['title'] = $name;
    } else {
        $params['title'] = $apibridge->get_default_seriestitle($courseid, $USER->id);
    }

    $params['contributor'] = $USER->firstname.' '.$USER->lastname;

    $api = api::get_instance();

    $newseries = str_replace('"', '', $api->oc_post('/v0/series', $params));
    $result = $apibridge->import_series_to_course_with_acl_change($courseid, $newseries, $USER->id);

    if ($result->error == 1) {
        \core\notification::error(get_string('error_createseries', 'local_ustreamseries'));
        return null;
    } else {
        $result->seriestitle = $apibridge->get_series_by_identifier($newseries)->title;
        return $result;
    }

}

/**
 * Creates a series in u:stream and connects it with moodle
 * @param int $courseid the id of the course for the series
 * @param string $identifyer string consisting of "TID_Term"
 * @return object|null $result object with further information
 */
function local_ustreamseries_create_lv_series($courseid, $identifyer = null) {
    global $USER;

    $apibridge = apibridge::get_instance();

    $params = [];
    $params['provider'] = 'univie';
    if ($identifyer) {
        list($tid, $term) = explode('_', $identifyer);
        $params['courseId'] = str_replace('-', '.', $tid);
        $tid;
        $params['term'] = $term;
    } else {
        \core\notification::error(get_string('error_createseries', 'local_ustreamseries'));
        return null;
    }

    $api = api::get_instance();

    $newseries = str_replace('"', '', $api->oc_put('/v0/campus/course/univie/enroll', $params));
    if ($newseries != "") {
        $result = $apibridge->import_series_to_course_with_acl_change($courseid, $newseries, $USER->id);
    } else {
        \core\notification::error(get_string('error_createseries', 'local_ustreamseries'));
        return null;
    }

    if ($result->error === 1) {
        \core\notification::error(get_string('error_createseries', 'local_ustreamseries'));
        return null;
    } else {
        $result->seriestitle = $apibridge->get_series_by_identifier($newseries)->title;
        return $result;
    }
}

/**
 * Check, if a series exists in opencast
 *
 * @param string $seriesid opencast series ID
 * @return bool $result whether the series exists
 */
function local_ustreamseries_check_series_exists($seriesid) {
    $api = apibridge::get_instance();
    try {
        $series = $api->get_series_by_identifier($seriesid);
    } catch (\moodle_exception $e) {
        \core\notification::error(get_string('error_reachustream', 'local_ustreamseries').$e->getMessage());
    }
    return (bool) $series;
}

/**
 * Check, whether a moodle course is a lv or not
 * @param int $courseid the courseid
 *
 * @return bool $result true/false
 */
function local_ustreamseries_is_lv($courseid) {
    global $DB;
    $shortname = $DB->get_field('course', 'shortname', ['id' => $courseid]);

    $islv = false;
    if (preg_match('/^\d{4}[WS] \d*-\d* .*/', $shortname) ||
        preg_match('/^[WS]S\d{4}-.*/', $shortname)) {
        $islv = true;
    }

    return $islv;
}

  /**
   * Check, whether a moodle user has write permission to a series in Opencast.
   *
   * Checks Opencast writing permission for the u:account connected to the given moodle-user.
   * @param string $ocseriesid Opncast seriesid to check permissions for
   * @param int $userid moodle user-ID to check permissions for. Should be the u:account
   * @throws \exception
   * @return bool $result true/false
   */
function local_ustreamseries_check_user_edit_permission($ocseriesid, $userid = null) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    if (is_siteadmin($userid)) {
        return true;
    }

    if (!ctype_xdigit(str_replace(' ', '', str_replace('-', '', $ocseriesid)))) {
        \core\notification::error(get_string('error_no_valid_seriesid', 'local_ustreamseries'));
        return false;
    }

    $api = api::get_instance();

    $response = $api->oc_get('/api/series/' . $ocseriesid . '/acl');

    if ($api->get_http_code() == 200) {
        $seriesacls = json_decode($response);
    } else if ($api->get_http_code() == 404) {
        \core\notification::error(get_string('error_no_series_found', 'local_ustreamseries', $ocseriesid));
        return false;
    } else {
        \core\notification::error(get_string('error_reachustream', 'local_ustreamseries').$response);
        return false;
    }

    $userisallowed = false;

    $thisrole = local_ustreamseries_muid_to_ocrole($userid);
    foreach ($seriesacls as $seriesacl) {
        if ($seriesacl->role == $thisrole && $seriesacl->allow == true && $seriesacl->action == 'write') {
            $userisallowed = true;
        }
    }

    if ($userisallowed == false) {
        $a = [];
        $a['username'] = $USER->username;
        $a['seriesid'] = $ocseriesid;
        \core\notification::error(get_string('error_noseriespermissions', 'local_ustreamseries', $a));
        return false;
    }

    return true;
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