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
$string['addnewseries'] = 'Add new series';
$string['button_noustream'] = 'Go to link u:stream';
$string['editexistingseries'] = 'Edit existing series';
$string['error_connectseries'] = 'There was an error to connect the series with ID {$a}.';
$string['error_coursenotfound'] = 'There was an error retrieving possible u:stream-Series to connect to the course with the ID {$a}.';
$string['error_createseries'] = 'There was a problem creating the series in u:stream!';
$string['error_createseries_noseriesname'] = 'Error creating series: No seriesname was given';
$string['error_noseriespermissions'] = 'User {$a->username} does not have edit-permissions on u:stream-series {$a->seriesid}! ';
$string['error_no_valid_seriesid'] = 'The given ID does not seem to be a valid u:stream series-ID.';
$string['error_no_seriesid'] = 'No series ID.';
$string['error_reachustream'] = 'The u:stream-server could not be reached: ';
$string['error_no_series_found'] = 'No series with ID {$a} found!';
$string['error_no_seriesname'] = 'No name for the new series provided.';
$string['instructions_noustream'] = 'Um "u:stream-Videos" verwenden zu können, müssen Sie:
<ol><li><b>Eine Serie einbinden</b>: Eine vorhandene u:stream-Serie  einbinden oder eine neue erstellen. Eine Serie bezeichnet eine Sammlung Videos. Alle Aufnahmen, die dieser Serie zugeordnet sind, werden unter u:stream-Videos aufgelistet, sobald Sie die Verknüpfung hergestellt haben. In einem Moodle-Kurs können mehrere Serien eingebunden werden. Bei Bedarf können Sie auch zusätzliche Videos über Moodle hochladen oder über u:stream-Studio aufnehmen.<br> </li>
<li><b>Einzelne Videos oder gesamte Serie im Kurs für Studierende bereitstellen:</b> Sie können entweder direkt über "u:stream-Videos" die Aktivitäten für den Kurs anlegen oder wie gewohnt mit "Arbeitsmaterial oder Aktivität hinzufügen".</li></ol>
Eine detailierte Anleitung finden Sie  hier (Link).';
$string['link_to_block'] = 'Go to u:stream video overview page.';
$string['link_stream_form_create'] = 'Create new Series';
$string['link_stream_form_create_lv'] = 'create LV-Series';
$string['link_stream_form_create_lv_series_course'] = 'Course to create a new u:stream series for';
$string['link_stream_form_create_personal'] = 'Personal Series';
$string['link_stream_form_createselect'] = 'Type of Series';
$string['link_stream_form_createselect_help'] = 'Type of Series. Longer helptext.';
$string['link_stream_form_link'] = 'Link series';
$string['link_stream_form_link_all_course_series'] = 'Link all course series';
$string['link_stream_form_link_other'] = 'Other';
$string['link_stream_form_linkselect'] = 'Series to import';
$string['link_stream_form_nooptionsavailable'] = 'No options available';
$string['link_stream_form_series_id'] = 'Series ID';
$string['link_stream_form_series_canntot_be_created'] = 'Series cannot be created.';
$string['link_stream_form_series_id_help'] = 'Series ID. Help text';
$string['link_stream_form_seriesalreadyconnected'] = 'Series already connected.';
$string['link_stream_form_seriesname'] = 'Name of the series in u:stream';
$string['link_stream_form_seriesnotexists'] = 'Series does either not exist.';
$string['link_stream_form_select_action'] = 'Choose action';
$string['link_stream_form_select_action_help'] = 'Choose action. Helptext.';
$string['link_stream_settingsmenu'] = 'Manage series';
$string['link_stream_settingsmenu_short'] = 'Manage u:stream-series';
$string['link_ustream'] = 'u:stream videos';
$string['navigationname'] = 'u:stream';
$string['pluginname'] = 'u:stream series connector';
$string['runbutton'] = 'Run';
$string['series_creation_success'] = 'Series {$a->title} created successfully.<br>For further operations (upload, record, publish) please go back to <a href={$a->link}>u:stream videos</a>';
$string['series_editable'] = 'WARNING! All series connected here are editable by ALL course instructors of this course. Only link your u:stream series if you are OK with that. We are working on a more appropriate solution. Thank you for your patience.';
$string['series_link_success'] = 'Series {$a->title} linked to this course successfully.<br>For further operations (upload, record, publish) please go back to <a href="{$a->link}">u:stream videos</a>';
$string['ustream'] = 'u:stream';
$string['ustreamseries:create'] = 'Create new ustream-series';
$string['ustreamseries:link'] = 'Link course series';
$string['ustreamseries:link_other'] = 'Link any series';
$string['ustreamvideos'] = 'u:stream videos';
$string['warning_noustream'] = 'At the moment there is no u:stream series connected with this course.';