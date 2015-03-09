<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for GIS link to formfield
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Gislink_Model extends ORM_Tree
{
	
	/**
	 * One-to-many relationship definition
	 * Using this so can allow for 0 (ie. some indicators won't have categories)
	 * @var array
	 */
	protected $has_many = array(
		'form_field',
	);

	/**
	 * Name of the child table for this model - recursive
	 * @var string
	 */ 
	protected $children = "form_field";
	
	/**
	 * Default sort order
	 * @var array
	 */
	protected $sorting = array("formfield_id" => "asc");

	
	// Database table name
	protected $table_name = 'gislink';
	
	protected static $gislinks;

	
	/**
	 * Validate an indicator array, then use array values to populate indicator
	 *
	 * @param array $array Values to check
	 * @param bool $save Saves the record when validation succeeds
	 * @return bool
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Pass on validation to parent and return
		return parent::validate($array, $save);
	}

	
	/**
	 * Extend the default ORM save to also update matching Category_Lang record if it exits
	 */
	public function save()
	{
		parent::save();
		
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		$this->db->query('UPDATE `'.$table_prefix.'indicator_lang` SET indicator_title = ?, indicator_description = ? WHERE indicator_id = ? AND locale = ?',
			$this->indicator_title, $this->indicator_description, $this->id, $this->locale
		);
	}
	

	/**
	 * Gets the list of GIS links from the database as an array
	 *
	 * @param int $gislink_id Database id of the gis link
	 * @param string $local Localization to use
	 * @return array
	 */
	public static function gislinks($gislink_id = NULL)
	{
		if (! isset(self::$gislinks))
		{
			$gislinks = ORM::factory('gislink')->find_all();
			
			self::$gislinks = array();
			foreach($gislinks as $gislink)
			{
				self::$gislinks[$gislink->id]['gislink_id'] = $gislink->id;
				self::$gislinks[$gislink->id]['gislink_formfield'] = $gislink->gislink_formfield;
				self::$gislinks[$gislink->id]['gislink_gisfile'] = $gislink->gislink_gisfile;
			}
		}
		
		if ($gislink_id)
		{
			return isset(self::$gislinks[$gislink_id]) ? array($gislink_id => self::$gislinks[$gislink_id]) : FALSE;
		}
		
		return self::$gislinks;
	}
	
	
	/**
	 * Checks if the specified category ID is of type INT and exists in the database
	 *
	 * @param	int	$indicator_id Database id of the indicator to be looked up
	 * @return	bool
	 */
	public static function is_valid_gislink($gislink_id)
	{
		return ( ! is_object($gislink_id) AND intval($gislink_id) > 0)
				? self::factory('gislink', intval($gislink_id))->loaded
				: FALSE;
	}

	
	/** 
	* Delete gislink
	*
	*/
	//public static function delete()
	//{
	//	$this->delete();
	//}
}
