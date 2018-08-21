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
 * Provides form to edit students attendance
 *
 * @package     mod_tals
 * @copyright   2017 Technische Hochschule Mittelhessen - University of Applied Sciences - Giessen, Germany
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/locallib.php');
require_once($CFG->libdir . '/accesslib.php');

global $DB;

// Course_module ID, or.
$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$appid = required_param('appid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// ... module instance id.
$t = optional_param('t', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('tals', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('tals', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($t) {
    $moduleinstance = $DB->get_record('tals', ['id' => $n], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $moduleinstance->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('tals', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'tals'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

// Check manager capabilities.
$capabilities = [
    'mod/tals:manage',
    'mod/tals:change',
    'mod/tals:viewreports'
];

if (!has_any_capability($capabilities, $modulecontext)) {
    print_error(get_string('nopermission', 'tals'));
}

// Get the users data for given appointment.
$appointment = $DB->get_record('tals_appointment', ['id' => $appid, 'courseid' => $courseid]);
$userlog = $DB->get_record('tals_log', ['userid' => $userid, 'courseid' => $courseid, 'fk_appointment_id' => $appid]);
$user = $DB->get_record('user', ['id' => $userid], 'id, username, firstname, lastname, email');

$PAGE->set_url('/mod/tals/edit.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

// CSS.

// Header.
echo '<ul id="liste">
    <li class="element"><a href="' . new moodle_url('/mod/tals/manage.php', ['id' => $id]) . '">'
    . get_string('label_header_date', 'tals') . '</a></li>
    <li class="element"><a href="' . new moodle_url('/mod/tals/add.php', ['id' => $id]) . '">'
    . get_string('label_header_add', 'tals') . '</a></li>
    <li class="element"><a href="' . new moodle_url('/mod/tals/report.php', ['id' => $id]) . '">'
    . get_string('label_header_report', 'tals') . '</a></li>
  </ul>';

echo '<div id="edit" class="tabcontent">
        <form action="' . new moodle_url('/mod/tals/editstudent.php',
        ['id' => $id, 'userid' => $userid, 'appid' => $appid, 'courseid' => $courseid]) . '" method="post" id="formular">
        <div class="rahmen" style="margin-bottom: 1em;">
            <p><h3>' . get_string('label_attendance_in', 'tals') . ' <b>' . $appointment->title . '</b></h3></p>
            <p>' . $user->firstname . ' ' . $user->lastname . '</p>
        </div>
          <table id="tabelle">
            <tbody>';

echo '<tr><th></th>';

echo '<th style="text-align: center;">' . get_string('Present_full', 'tals') . '</th>
      <th style="text-align: center;">' . get_string('Absent_full', 'tals') . '</th>
      <th style="text-align: center;">' . get_string('Excused_full', 'tals') . '</th>
    </tr>
      <tr>
      <td><b>' . get_string('label_status', 'tals') . '</b></td>';

// Default value absent.
$check = 2;
$comment = "";
$out = "";

if ($userlog) {
    $check = $userlog->fk_type_attendance_id;
    $comment = $userlog->comment;
}

if ($check == 1) {
    echo '<td class="cell"><input type="radio" value="1" name="attendance" id="P" checked="checked"></td>
            <td class="cell"><input type="radio" value="2" name="attendance" id="A"></td>
            <td class="cell"><input type="radio" value="3" name="attendance" id="E"></td>';
} else if ($check == 2) {
    echo '<td class="cell"><input type="radio" value="1" name="attendance" id="P"></td>
            <td class="cell"><input type="radio" value="2" name="attendance" id="A" checked="checked"></td>
            <td class="cell"><input type="radio" value="3" name="attendance" id="E"></td>';
} else if ($check == 3) {
    echo '<td class="cell"><input type="radio" value="1" name="attendance" id="P"></td>
            <td class="cell"><input type="radio" value="2" name="attendance" id="A"></td>
            <td class="cell"><input type="radio" value="3" name="attendance" id="E" checked="checked"></td>';
}

echo '</tr>
      </tbody>
      </table>

      <table style="margin-top: 1em;">
        <tr>
            <td><p class="description">' . get_string('label_comment', 'tals') . '</p></td>
        </tr>
        <tr>
            <td><textarea id="comment" name="comment" style="width: 250px; height: 100px;">' . $comment . '</textarea></td>
        </tr>
      </table>


      <input type="submit" name="submit" value="' . get_string('label_safe', 'tals') . '" style="border-radius: 0.4em;">
    </form>

</div>';

echo $OUTPUT->footer();