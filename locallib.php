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
 * Connect an opencast series with moodle.
 * @param int $customsemid db id of the customfield
 */
function local_ustreamseries_connect($courseid, $seriesid) {
    global $DB;
    //TODO
}

function local_ustreamseries_get_all_unconnected_course_series($courseid) {
    //TODO returns array of ['id' => 'name'];
}

function local_ustreamseries_get_connected_course_series($course) {
    //TODO
    return ['asdf' => 'This series is connected'];
    //TODO returns array of ['id' => 'name'];
}

function local_ustreamseries_create_series($courseid, $courseseries, $name) {
    
}

function local_ustreamseries_check_series_exists($seriesid) {
    //TODO
    return true;
}

function local_ustreamseries_is_lv($courseid) {
    return true;
}