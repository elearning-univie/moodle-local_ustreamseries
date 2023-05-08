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
 * This is the version.php for local_ustreamseries.
 *
 * @package   local_ustreamseries
 * @copyright 2021, University of Vienna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2023050800;
$plugin->requires = 2020110900;
$plugin->component = 'local_ustreamseries';
$plugin->maturity = MATURITY_STABLE;
$plugin->release = 'v0.1.8';

$plugin->dependencies = [
  'block_opencast' => 2022111900,
  'tool_opencast' => 2022111900,
  'mod_streamlti' => 2021051100
];
