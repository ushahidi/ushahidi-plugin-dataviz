<?php defined('SYSPATH') or die('No direct script access.');
/**
 * DataViz Plugin installer
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Sara Terp <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   DataViz Plugin	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/
class dataviz_Install {

	/** Constructor to load the shared database library
    */
    public function __construct()
    {
        $this->db = Database::instance();
    }
  
    /** Creates the required database tables for the plugin */
    public function run_install()
    {
        // Create the database tables.
        // Also include table_prefix in name
        $this->db->query("CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."gisfile` (".
                "id int(11) unsigned NOT NULL AUTO_INCREMENT, ".
                "gisfile_description varchar(255) DEFAULT NULL, ".
                "gisfile_filename varchar(100) DEFAULT NULL, ".
                "gisfile_options text DEFAULT NULL, ".
                "gisfile_formfields text DEFAULT NULL, ".
                "gisfile_xpos int DEFAULT 0, ".
                "gisfile_ypos int DEFAULT 0, ".
                "gisfile_width int DEFAULT 1000, ".
                "locale varchar(10) DEFAULT NULL, ".
                "PRIMARY KEY (`id`)
            );
        ");

        // Add default set of gisfiles to the database list, if they exist
        $default_gisfiles = [
            ['KEN_admin1.json', "Kenya admin level 1"], 
            ['TZA_admin1.json', "Tanzania admin level 1"], 
            ['YEM_admin1.json', "Yemen admin level 1"]];
        $geojson = new geojson;
        foreach ($default_gisfiles as $default_gisfile)
        {
            #Only store default file details if it exists in the upload directory
            if (file_exists(Kohana::config('upload.directory', TRUE).$default_gisfile[0]))
            {
                // Search gisfiles table for this filename. If not found, add to list. 
                // Will need to sniff file for its list of gis regions before populating the 
                // options field. 
                $db_gisfile = ORM::factory('gisfile')
                    ->where('gisfile_filename', $default_gisfile[0])->find();
                echo $db_gisfile;
                if (!$db_gisfile->loaded) 
                {
                    //Add gisfile description to database
                    $gisfile = ORM::factory('gisfile');
                    $gisfile->gisfile_description = $default_gisfile[1];
                    $gisfile->gisfile_filename = $default_gisfile[0];
                    $geometry = $geojson->get_geometry($default_gisfile[0]);
                    $gisfile->gisfile_options = $geometry['options'];
                    $gisfile->gisfile_xpos    = $geometry['xpos'];
                    $gisfile->gisfile_ypos    = $geometry['ypos'];
                    $gisfile->gisfile_width   = $geometry['width'];
                    //FIXIT: add call here to set x,y,width from topojson file
                    $gisfile->save();
                }
            }   
        }
    }
 
    /** Deletes the database tables for the actionable module */
    public function uninstall()
    {$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'gisfile`');
 
    }
}