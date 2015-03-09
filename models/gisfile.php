<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for GIS files used in choropleths
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

class Gisfile_Model extends ORM_Tree
{
		
	/**
	 * Default sort order
	 * @var array
	 */
	protected $sorting = array("gisfile_description" => "asc");

	
	// Database table name
	protected $table_name = 'gisfile';
	
	protected static $gisfiles;

	
	/**
	 * Validate a gisfile array, then use array values to populate gisfile
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
		
		$this->db->query('UPDATE `'.$table_prefix.'gisfile` SET gisfile_description = ?, gisfile_filename = ?, gisfile_options = ?, gisfile_formfields = ? WHERE id = ? AND locale = ?',
			$this->gisfile_description, $this->gisfile_filename, $this->gisfile_options, $this->gisfile_formfields, $this->id, $this->locale
		);
	}
	

	/**
	 * Gets the list of GIS links from the database as an array
	 *
	 * @param int $gislink_id Database id of the gis link
	 * @param string $local Localization to use
	 * @return array
	 */
	public static function gisfiles($gisfile_id = NULL)
	{
		if (! isset(self::$gisfiles))
		{
			$gisfiles = ORM::factory('gisfile')->find_all();
			
			self::$gisfiles = array();
			foreach($gisfiles as $gisfile)
			{
				self::$gisfiles[$gisfile->id]['id'] = $gisfile->id;
				self::$gisfiles[$gisfile->id]['gisfile_description'] = $gisfile->gisfile_description;
				self::$gisfiles[$gisfile->id]['gisfile_formfields'] = $gisfile->gisfile_formfields;
				self::$gisfiles[$gisfile->id]['gisfile_filename'] = $gisfile->gisfile_filename;
				self::$gisfiles[$gisfile->id]['gisfile_options'] = $gisfile->gisfile_options;
			}
		}
		
		if ($gisfile_id)
		{
			return isset(self::$gisfiles[$gisfile_id]) ? array($gisfile_id => self::$gisfiles[$gisfile_id]) : FALSE;
		}
		
		return self::$gisfiles;
	}
	
	
	/**
	 * Checks if the specified category ID is of type INT and exists in the database
	 *
	 * @param	int	$indicator_id Database id of the indicator to be looked up
	 * @return	bool
	 */
	public static function is_valid_gisfile($gisfile_id)
	{
		return ( ! is_object($gisfile_id) AND intval($gisfile_id) > 0)
				? self::factory('gisfile', intval($gisfile_id))->loaded
				: FALSE;
	}

	
	/** 
	* Delete gisfile
	*
	*/
	//public static function delete()
	//{
	//	$this->delete();
	//}
}
