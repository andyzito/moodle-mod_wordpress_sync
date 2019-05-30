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
 * Plugin administration pages are defined here.
 *
 * @package     mod_wordpresssync
 * @category    admin
 * @copyright   2018 Lafayette College
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/mod/wordpresssync/lib.php');

if ($ADMIN->fulltree) {
    global $DB;

    $pluginname = get_string('pluginname', 'mod_wordpresssync');
    $settings = new theme_boost_admin_settingspage_tabs('mod_wordpresssync', $pluginname);

    // # TAB: Main settings.
    $page = new admin_settingpage('mod_wordpresssync_main',
            get_string('settings:sections:main', 'mod_wordpresssync'));

    // Behavior when course ends.
    $course_end_behavior_options = array(
        MOD_WPSYNC_COURSE_END_BEHAVIOR_DO_NOTHING => 'Do nothing',
        MOD_WPSYNC_COURSE_END_BEHAVIOR_ARCHIVE    => 'Archive the WP site(s)',
        MOD_WPSYNC_COURSE_END_BEHAVIOR_DELETE     => 'Delete the WP site(s)',
    );

    $page->add(new admin_setting_configselect('mod_wordpresssync/course_end_behavior',
            get_string('settings:course_end_behavior:desc', 'mod_wordpresssync'),
            get_string('settings:course_end_behavior:subdesc', 'mod_wordpresssync'),
            MOD_WPSYNC_COURSE_END_BEHAVIOR_DO_NOTHING,
            $course_end_behavior_options
    ));
    // ------------------------------

    // Behavior when a user is removed, if user sites are active.
    $user_removed_behavior_options = array(
        MOD_WPSYNC_USER_REMOVED_BEHAVIOR_DO_NOTHING => 'Do nothing',
        MOD_WPSYNC_USER_REMOVED_BEHAVIOR_ARCHIVE    => 'Archive the user\'s WP site',
        MOD_WPSYNC_USER_REMOVED_BEHAVIOR_DELETE     => 'Delete the user\'s WP site',
    );

    $page->add(new admin_setting_configselect('mod_wordpresssync/course_end_behavior',
            get_string('settings:user_removed_behavior:desc', 'mod_wordpresssync'),
            get_string('settings:user_removed_behavior:subdesc', 'mod_wordpresssync'),
            MOD_WPSYNC_USER_REMOVED_BEHAVIOR_ARCHIVE,
            $user_removed_behavior_options
    ));
    // ------------------------------
    // END TAB: Main settings.

    // # TAB: Instance defaults: Main.
    $page = new admin_settingpage('mod_wordpresssync_defaults_main',
            get_string('settings:sections:instance_defaults_main', 'mod_wordpresssync'));

    // Create main course site.
    $page->add(new admin_setting_configcheckbox('mod_wordpresssync/default_course_site',
            get_string('settings:default_course_site:desc', 'mod_wordpresssync'),
            get_string('settings:default_course_site:subdesc', 'mod_wordpresssync'),
            1));
    // ------------------------------

    // Create sites for each user.
    $page->add(new admin_setting_configcheckbox('mod_wordpresssync/default_user_sites',
            get_string('settings:default_user_sites:desc', 'mod_wordpresssync'),
            get_string('settings:default_user_sites:subdesc', 'mod_wordpresssync'),
            0));
    // ------------------------------

    // Should we remove users from the WP site(s) who are not enrolled in the Moodle course?
    $page->add(new admin_setting_configcheckbox('mod_wordpresssync/remove_unenrolled_wp_users',
            get_string('settings:remove_unenrolled_wp_users:desc', 'mod_wordpresssync'),
            get_string('settings:remove_unenrolled_wp_users:subdesc', 'mod_wordpresssync'),
            0));
    // ------------------------------
    // END TAB: Instance defaults: Main.

    // TAB: Instance defaults: Course site.
    $page = new admin_settingpage('mod_wordpresssync_defaults_course_site',
            get_string('settings:sections:instance_defaults_course_site', 'mod_wordpresssync'));

    // Roles with access to the main course site.
    $roles = $DB->get_records_menu('role', null, '', 'id,shortname');
    $contextlevels = $DB->get_records_menu('role_context_levels', array('roleid' => $k), '', 'id,contextlevel');
    $roles = array_filter($options, function($v, $k) use ($contextlevels) {
        return in_array(CONTEXT_COURSE, array_values($contextlevels));
    }, ARRAY_FILTER_USE_BOTH);
    $roles = array_merge(
        array( 0 => 'All'),
        $roles
    );

    $page->add(new admin_setting_configselect('mod_wordpresssync/default_course_site_access_roles',
            get_string('settings:course_site_access_roles:desc', 'mod_wordpresssync'),
            get_string('settings:course_site_access_roles:subdesc', 'mod_wordpresssync'),
            0,
            $roles));
    // ------------------------------

    // Default course site title.
    $page->add(new admin_setting_configtext('mod_wordpresssync/default_course_site_title',
            get_string('settings:default_course_site_title:desc', 'mod_wordpresssync'),
            get_string('settings:default_course_site_title:subdesc', 'mod_wordpresssync'),
            get_string('settings:defaults:default_course_site_title:subdesc', 'mod_wordpresssync')));
    // ------------------------------

    // Default course site tagline.
    $page->add(new admin_setting_configtext('mod_wordpresssync/default_course_site_tagline',
            get_string('settings:default_course_site_tagline:desc', 'mod_wordpresssync'),
            get_string('settings:default_course_site_tagline:subdesc', 'mod_wordpresssync'),
            get_string('settings:defaults:default_course_site_tagline:subdesc', 'mod_wordpresssync')));
    // ------------------------------
    // END TAB: Instance defaults: Course site.

    // TAB: Instance defaults: User sites.
    $page = new admin_settingpage('mod_wordpresssync_defaults_user_sites',
            get_string('settings:sections:instance_defaults_user_sites', 'mod_wordpresssync'));

    // Roles with access to each user site (in addition to site owner).
    $page->add(new admin_setting_configselect('mod_wordpresssync/default_user_sites_access_roles',
            get_string('settings:default_user_sites_access_roles:desc', 'mod_wordpresssync'),
            get_string('settings:default_user_sites_access_roles:subdesc', 'mod_wordpresssync'),
            0,
            $roles));
    // ------------------------------

    // Default user site titles.
    $page->add(new admin_setting_configtext('mod_wordpresssync/default_user_sites_title',
            get_string('settings:default_user_sites_title:desc', 'mod_wordpresssync'),
            get_string('settings:default_user_sites_title:subdesc', 'mod_wordpresssync'),
            get_string('settings:defaults:default_user_sites_title:subdesc', 'mod_wordpresssync')));
    // ------------------------------

    // Default user site taglines.
    $page->add(new admin_setting_configtext('mod_wordpresssync/default_user_sites_tagline',
            get_string('settings:default_user_sites_tagline:desc', 'mod_wordpresssync'),
            get_string('settings:default_user_sites_tagline:subdesc', 'mod_wordpresssync'),
            get_string('settings:defaults:default_user_sites_tagline:subdesc', 'mod_wordpresssync')));
    // ------------------------------
    // END TAB: Instance defaults: Course site.
}
