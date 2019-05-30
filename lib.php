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
 * Library of interface functions and constants.
 *
 * @package     mod_wordpresssync
 * @copyright   2018 Lafayette College
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// defined('MOODLE_INTERNAL') || die();

define('MOD_WPSYNC_COURSE_END_BEHAVIOR_DO_NOTHING', 0);
define('MOD_WPSYNC_COURSE_END_BEHAVIOR_ARCHIVE', 1);
define('MOD_WPSYNC_COURSE_END_BEHAVIOR_DELETE', 2);

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function mod_wordpresssync_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_wordpresssync into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_wordpresssync_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function mod_wordpresssync_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('mod_wordpresssync', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_wordpresssync in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_wordpresssync_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function mod_wordpresssync_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('mod_wordpresssync', $moduleinstance);
}

/**
 * Removes an instance of the mod_wordpresssync from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function mod_wordpresssync_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('mod_wordpresssync', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('mod_wordpresssync', array('id' => $id));

    return true;
}
