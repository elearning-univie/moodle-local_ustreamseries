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

namespace local_ustreamseries\hooks;

use core\hook\output\before_standard_top_of_body_html_generation;

/**
 * Allows the plugin to add any elements to the footer.
 *
 * @package    tool_policy
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class callbacks {
    /**
     * JS for inserting a link to the ustreamseries-dialogue and changing the title.
     *
     * @return string|void
     * @throws coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function before_standard_top_of_body_html_generation(before_standard_top_of_body_html_generation $hook): void {
        global $PAGE, $COURSE, $OUTPUT;
        $context = \context_course::instance($COURSE->id);
        if (!(strpos($PAGE->url->get_path(), '/blocks/opencast/index.php') !== false) || !has_any_capability(
            ['local/ustreamseries:create_personal', 'local/ustreamseries:create_lv',
                'local/ustreamseries:link_lv', 'local/ustreamseries:link_other'],
            $context)) {
                return;
            }
        $templatecontext = [];
        $link = new \moodle_url('/local/ustreamseries/link_stream.php', ['id' => $COURSE->id]);
        $templatecontext['url'] = $link->out();
        $templatecontext['title'] = get_string('link_stream_settingsmenu_short', 'local_ustreamseries');
        $templatecontext['text'] = get_string('link_stream_settingsmenu', 'local_ustreamseries');
        $rendered = $OUTPUT->render_from_template('local_ustreamseries/series_link', $templatecontext);
        $pluginname = get_string('pluginname', 'block_opencast');
        $PAGE->requires->js_call_amd('local_ustreamseries/blockindexpatches', 'init', [$rendered, $COURSE->fullname, $pluginname]);
    }
}