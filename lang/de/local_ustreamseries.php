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
 * Local language pack from https://moodletest.univie.ac.at
 *
 * @package    local_ustreamseries
 * @subpackage ustreamseries
 * @copyright     2022 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addnewseries'] = 'u:stream-Serien einbinden';
$string['button_noustream'] = 'Weiter zum Serien einbinden';
$string['editexistingseries'] = 'In diesem Kurs eingebundene u:stream-Serien';
$string['error_connectseries'] = 'Beim Verbinden der Serie {$a} trat ein Fehler auf.';
$string['error_coursenotfound'] = 'Beim Abrufen möglicher u:stream-Serien für den Kurs mit der ID {$a} trat ein Fehler auf.';
$string['error_createseries'] = 'Beim Anlegen der Serie in u:stream trat ein Fehler auf.';
$string['error_createseries_noseriesname'] = 'Fehler beim Erstellen der Serie: kein Serienname wurde angegeben.';
$string['error_noseriespermissions'] = 'Der Benutzer {$a->username} hat keine Bearbeitungsrechte für die u:stream-Serie {$a->seriesid}!<br>Bitte den Support kontaktieren.';
$string['error_no_valid_seriesid'] = 'Die angegebene ID scheint keine valide u:stream Serien-ID zu sein.';
$string['error_no_seriesid'] = 'Es wurde keine Serien ID angegeben.';
$string['error_reachustream'] = 'Der u:stream-server konnte nicht erreicht werden.';
$string['error_no_series_found'] = 'Es konnte keine Serie mit der ID {$a} gefunden werden.';
$string['error_no_seriesname'] = 'Es wurde kein Name für die neue Serie angegeben.';
$string['instructions_noustream'] = 'Um "u:stream-Videos" verwenden zu können, müssen Sie:
<ol><li><b>Eine Serie einbinden</b>: Eine vorhandene u:stream-Serie  einbinden oder eine neue erstellen. Eine Serie bezeichnet eine Sammlung Videos. Alle Aufnahmen, die dieser Serie zugeordnet sind, werden unter u:stream-Videos aufgelistet, sobald Sie die Verknüpfung hergestellt haben. In einem Moodle-Kurs können mehrere Serien eingebunden werden. Bei Bedarf können Sie auch zusätzliche Videos über Moodle hochladen oder über u:stream-Studio aufnehmen.<br> </li>
<li><b>Einzelne Videos oder gesamte Serie im Kurs für Studierende bereitstellen:</b> Sie können entweder direkt über "u:stream-Videos" die Aktivitäten für den Kurs anlegen oder wie gewohnt mit "Arbeitsmaterial oder Aktivität hinzufügen".</li></ol>
Eine detailierte Anleitung finden Sie  hier (Link).';
$string['link_stream_form_create'] = 'neue (benutzerdefinierte) Serie erstellen';
$string['link_stream_form_create_lv'] = 'neue LV-Serie erstellen und einbinden';
$string['link_stream_form_create_lv_series_course'] = 'Course to create a new u:stream series for';
$string['link_stream_form_create_personal'] = 'Persönliche Serie';
$string['link_stream_form_createselect'] = 'Serientyp';
$string['link_stream_form_createselect_help'] = 'Serientyp. Längerer Hilfetext.';
$string['link_stream_form_link'] = 'vorhandene u:stream-Serie verknüpfen';
$string['link_stream_form_link_all_course_series'] = 'alle diesem Moodle-Kurs zugeordnete LVs einbinden';
$string['link_stream_form_link_other'] = 'eine vorhandene Serie einbinden';
$string['link_stream_form_linkselect'] = 'Serie auswählen';
$string['link_stream_form_nooptionsavailable'] = 'Keine Auswahlmöglichkeiten verfügbar';
$string['link_stream_form_select_action'] = 'Aktion auswählen';
$string['link_stream_form_select_action_help'] = 'Aktion auswählen. Hilfetext';
$string['link_stream_form_seriesalreadyconnected'] = 'Serie bereits verbunden.';
$string['link_stream_form_series_id'] = 'Series ID';
$string['link_stream_form_series_id_help'] = 'Serien-ID. Hilfetext.';
$string['link_stream_form_seriesname'] = 'Titel u:stream-Serie';
$string['link_stream_form_seriesnotexists'] = 'Serie existiert nicht.';
$string['link_stream_settingsmenu'] = 'u:stream-Serien verwalten';
$string['link_to_block'] = 'Zurück zu u:stream-Videos';
$string['link_ustream'] = 'u:stream-Videos';
$string['navigationname'] = 'u:stream-Videos';
$string['series_creation_success'] = 'Serie {$a->title} erfolgreich erstellt. <br> Für weitere Aktionen (Upload, Aufnahme, Veröffentlichung) gehen Sie <a href="{$a->link}"> zurück zu u:stream-Videos</a>.';
$string['series_editable'] = '<b>HINWEIS: Alle hier eingebundenen Serien sind von ALLEN eingeschriebenen LEHRENDEN/TUTOR*INNEN/SACHBEARBEITER*INNEN über Moodle editierbar. Darüber hinaus erhalten die Personen auch Berechtigungen auf u:stream Seite.</b> <br>Verlinken Sie nur u:stream Serien in Moodle Kursen, wo eine Bearbeitung (Videos hochladen/löschen/schneiden/...) von dieser Personengruppe gewünscht ist - ansonsten binden Sie ihre u:stream Serie wie bisher über die Aktivität u:stream ein (Anleitung).';
$string['series_link_success'] = 'Serie {$a->title} erfolgreich erstellt. <br> Für weitere Aktionen (Upload, Aufnahme, Veröffentlichung) gehen Sie <a href="{$a->link}"> zurück zu u:stream-Videos</a>.';
$string['ustreamseries:create'] = 'Neue benutzerdefinierte u:stream Serie erstellen';
$string['ustreamseries:link'] = 'vorhandene LV-Serie aus u:stream einbinden';
$string['ustreamseries:link_other'] = 'beliebige u:stream Serie einbinden';
$string['ustreamvideos'] = 'u:stream-Videos';
$string['warning_noustream'] = '<strong>Bisher ist keine u:stream-Serie mit diesem Kurs verknüpft.</strong>';