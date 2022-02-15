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
 * Strings for component 'local_contactlist', language 'en'.
 *
 * @package       local_ustreamseries
 * @author        Thomas Wedekind
 * @copyright     2022 University of Vienna
 * @since         Moodle 3.11
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['error_connectseries'] = 'There was an error to connect the series with ID {$a}.';
$string['error_coursenotfound'] = 'There was an error retrieving possible u:stream-Series to connect to the course with the ID {$a}.';
$string['error_createseries'] = 'There was a problem creating the series in u:stream!';
$string['error_noseriespermissions'] = 'User {$a->username} does not have edit-permissions on u:stream-series {$a->seriesid}! ';
$string['error_noseriesid'] = 'The given ID does not seem to be a valid u:stream series-ID.';
$string['error_reachustream'] = 'There was a problem to reach the u:stream-server!';
$string['error_no_series_found'] = 'No series with ID {$a} found!';
$string['link_ustream'] = 'Link u:stream series';
$string['link_stream_form_create'] = 'Create new series';
$string['link_stream_form_create_lv'] = 'Create new course specific series';
$string['link_stream_form_link'] = 'Link course series';
$string['link_stream_form_link_other'] = 'Link any ustream series';
$string['link_stream_form_no_course_series_to_connect'] = 'There are currently no series to connect to. Please create a series in u:stream first!';
$string['link_stream_form_no_course_series_to_connect_help'] = 'Visit this link ...!';
$string['link_stream_form_select_action'] = 'Choose action';
$string['link_stream_form_select_action_help'] = 'Help text describing the actions';
$string['link_stream_form_seriesname'] = 'Name of the series in u:stream';
$string['link_stream_form_link_all_course_series'] = 'Link all course series';
$string['link_stream_form_link_to_block'] = '<a href="{$a}">Go to u:stream video overview page.</a>';
$string['link_stream_form_series_id_select'] = 'Series to import';
$string['link_stream_form_series_id'] = 'Series ID';
$string['link_stream_form_series_id_help'] = 'Series ID as presented in the notification e-Mail when creating the series bla bla';
$string['link_stream_form_seriesalreadyconnected'] = 'The selected series is already connected to this course!';
$string['link_stream_form_seriesnotexistsorconnected'] = 'This Series does not exist anymore or is already connected.';
$string['link_stream_settingsmenu'] = 'Link new u:stream to this course';
$string['navigationname'] = 'u:stream';
$string['no_series'] = 'There is no series available!';
$string['pluginname'] = 'u:stream series connector';
$string['runbutton'] = 'Run';
$string['series_editable'] = 'WARNING! All series connected here are editable by ALL course instructors of this course. Only link your u:stream series if you are OK with that. We are working on a more appropriate solution. Thank you for your patience.';
$string['series_creation_success'] = 'Series "{$a}" created successfully';
$string['series_creation_failed'] = 'Error while creating series';
$string['series_link_failed'] = 'Error while linking series';
$string['series_link_success'] = 'Series "{$a}" linked to this course successfully';
$string['ustream'] = 'u:stream';
$string['ustreamseries:create'] = 'Create new ustream-series';
$string['ustreamseries:link'] = 'Link course series';
$string['ustreamseries:link_other'] = 'Link any series';
$string['warning_noustream'] = 'At the moment there is no u:stream series connected with this course. You can link your ustream-series with this course in the course settings under "Link u:stream series" or by clicking <a href={$a}>here</a>';