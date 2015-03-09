<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dataviz Hook.
 * This hook adds a link to the admin menubar
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
// Hook into the nav main_top
class dataviz {
	public function __construct()
	{
		// Hook into routing
		//Event::add('ushahidi_action.nav_admin_manage', array($this, '_add_nav'));
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	//public function _add_nav()
	public function add()
	{
		//Add a sub-nav link
		Event::add('ushahidi_action.nav_admin_manage', array($this, '_add_nav'));
	}

	public function _add_nav()
	{
		$menu = Event::$data;
		// Add plugin link to nav_admin_main_top
		echo "<a href=\"" . url::site() . "admin/dataviz\">Visuals</a>";

	}
	
}
new dataviz();
