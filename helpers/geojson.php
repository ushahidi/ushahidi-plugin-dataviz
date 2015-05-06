<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Geojson helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Sara Terp <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     DataViz Plugin   
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/
class geojson {
   
    /*
     * The get_geometries function creates a list of choropleth options from a geojson file
     */
    public function get_geometry($gisfilename) {

        $full_gisfilename = Kohana::config('upload.directory').'/'.$gisfilename;
        $filetext = file_get_contents($full_gisfilename);
        $filejson = json_decode($filetext);
        $options = [];
        $geometries = $filejson->objects->subunits->geometries;
        foreach ($geometries as $geometry)
        {
            if (isset($geometry->id))
            {
                $options[] = $geometry->id;
            }
        }
        $optstring = implode(',',$options);

        $bbox = $filejson->bbox;
        $xpos = ($bbox[0] + $bbox[2])/2.0;
        $ypos = ($bbox[1] + $bbox[3])/2.0;
        $width = ($bbox[2] - $bbox[0]);

        $geometry = array();
        $geometry['options'] = $optstring;
        $geometry['xpos'] = $xpos;
        $geometry['ypos'] = $ypos;
        $geometry['width'] = $width;

        return $geometry;
    }
}
