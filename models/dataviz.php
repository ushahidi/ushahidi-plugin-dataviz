<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Data Visualisations
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Sara Terp
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Dataviz_Model extends ORM {

	static $time_out = 1;


	/*
	* Normalise count data to be between 0 and maxvalue (this is needed by visualisation code)
	* Input is a 2d array with columns 0=label, 1=value
	*/
	private function normalise_counts($counts, $numreports, $maxvalue=100)
	{
		$normalised_counts = array();
		foreach ($counts as $cc)
		{
			$normalised_counts[] = [$cc[0], round($cc[1]*$maxvalue/$numreports,0)];
		}

		return $normalised_counts;
	}


	/*
	* get an array of report counts by date
	* @param dp1 - Arbitrary date range. Low date. YYYY-MM-DD
	* @param dp2 - Arbitrary date range. High date. YYYY-MM-DD
	* @param category - Only count reports under this category
	* @param approved - Only count approved reports if true
	*/
	static function get_counts_by_date($fromdatetime=NULL, $todatetime=NULL, $category=0, $formfield='', $approved=FALSE)
	{
		$direction = 'ASC'; //Switch: puts chart in ascending order; options are ASC or DESC

		// Tidy up time bound inputs
		if ($fromdatetime === NULL)
		{
			$fromdatetime = '1970-01-01 00:00:00';
		}

		if ($todatetime === NULL)
		{
			$todatetime = '3000-01-01 23:59:00';
		}

		// Get all reports in the top-level category (or all reports if category==0), 
		$table_prefix = Kohana::config('database.default.table_prefix');
		$sql = 'SELECT '
			. "DATE_FORMAT(i.incident_date, '%d-%m-%Y') as date, COUNT(incident_id) as count ";
		$sql .=  'FROM '.$table_prefix.'incident_category ic '
			. 'LEFT JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) '
			. 'LEFT JOIN '.$table_prefix.'incident i ON (ic.incident_id = i.id) ';
		$sql .= 'WHERE (c.id = '.$category.' OR c.parent_id = '.$category.') ';
		#Filter reports by date and active (if set)
		$sql .= "AND i.incident_date >= STR_TO_DATE('".$fromdatetime."', '%Y-%m-%d %H:%i:%s') "
			. "AND i.incident_date <= STR_TO_DATE('".$todatetime."', '%Y-%m-%d %H:%i:%s')".' ';
		if ($approved)
		{
			$sql .= 'AND i.incident_active = 1 ';
		}
		$sql .=	'GROUP BY date ORDER BY i.incident_date '.$direction.' ';
		$date_count_sql = Database::instance()->query($sql);
		$date_counts = array();
		foreach ($date_count_sql as $datecount) {
			$date_counts[] = [(string)$datecount->date, (integer)$datecount->count];
		}

		// Get total number of reports on those dates (counts might not be independant above)
		$sql = 'SELECT COUNT(incident_id) AS count '
			. 'FROM '.$table_prefix.'incident_category ic '
			. 'LEFT JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) '
			. 'LEFT JOIN '.$table_prefix.'incident i ON (ic.incident_id = i.id) '
			. 'WHERE (c.id = '.$category.' OR c.parent_id = '.$category.') ';
		$sql_num_reports = Database::instance()->query($sql);
		$num_reports = (integer)$sql_num_reports[0]->count;

		$data = array();
		$data['counts']  = $date_counts;
		$data['vizdata'] = $date_counts; //yes, yes, it's repeated - just in case need to modify for viz
		$data['num_reports'] = $num_reports;
		$data['earliest_datetime'] = $fromdatetime;
		$data['latest_datetime'] = $todatetime;

		return $data;
	}


	/*
	* get an array of report counts
	* @param dp1 - Arbitrary date range. Low date. YYYY-MM-DD
	* @param dp2 - Arbitrary date range. High date. YYYY-MM-DD
	* @param category - Only count reports under this category
	* @param approved - Only count approved reports if true
	*/
	static function get_category_counts($fromdatetime=NULL, $todatetime=NULL, $category=0, $formfield='', $approved=FALSE)
	{
		$direction = 'ASC'; //Switch: puts chart in ascending order; options are ASC or DESC

		// Tidy up time bound inputs
		if ($fromdatetime === NULL)
		{
			$fromdatetime = '1970-01-01 00:00:00';
		}

		if ($todatetime === NULL)
		{
			$todatetime = '3000-01-01 23:59:00';
		}

		// Get all reports in the top-level category (or all reports if category==0), 
		$table_prefix = Kohana::config('database.default.table_prefix');
		$sql = 'SELECT '
			. 'c.category_title, COUNT(incident_id) as count ';
		$sql .=  'FROM '.$table_prefix.'incident_category ic '
			. 'LEFT JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) '
			. 'LEFT JOIN '.$table_prefix.'incident i ON (ic.incident_id = i.id) ';
		$sql .= 'WHERE (c.id = '.$category.' OR c.parent_id = '.$category.') ';
		#Filter reports by date and active (if set)
		$sql .= "AND i.incident_date >= STR_TO_DATE('".$fromdatetime."', '%Y-%m-%d %H:%i:%s') "
			. "AND i.incident_date <= STR_TO_DATE('".$todatetime."', '%Y-%m-%d %H:%i:%s')".' ';
		if ($approved)
		{
			$sql .= 'AND i.incident_active = 1 ';
		}
		$sql .=	'GROUP BY category_id ORDER BY count '.$direction.' ';
		$category_count_sql = Database::instance()->query($sql);
		$category_counts = array();
		foreach ($category_count_sql as $catcount) {
			$category_counts[] = [(string)$catcount->category_title, (integer)$catcount->count];
		}

		// Get total number of reports in these categories (counts might not be independant above)
		$sql = 'SELECT COUNT(incident_id) AS count '
			. 'FROM '.$table_prefix.'incident_category ic '
			. 'LEFT JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) '
			. 'LEFT JOIN '.$table_prefix.'incident i ON (ic.incident_id = i.id) '
			. 'WHERE (c.id = '.$category.' OR c.parent_id = '.$category.') ';
		$sql_num_reports = Database::instance()->query($sql);
		$num_reports = (integer)$sql_num_reports[0]->count;

		$data = array();
		$data['counts'] = $category_counts;
		$data['vizdata'] = self::normalise_counts($category_counts, $num_reports, 100);
		$data['num_reports'] = $num_reports;
		$data['earliest_datetime'] = $fromdatetime;
		$data['latest_datetime'] = $todatetime;

		return $data;
	}

	/*
	* get an array of report counts for each admin1 region
	* ASSUMES that all reports have an admin1 field.
	* @param dp1 - Arbitrary date range. Low date. YYYY-MM-DD
	* @param dp2 - Arbitrary date range. High date. YYYY-MM-DD
	* @param category - Only count reports under this category
	* @param approved - Only count approved reports if true
	*/
	static function get_region_counts($fromdatetime=NULL, $todatetime=NULL, $category=0, $formfieldname='', $approved=FALSE)
	{
		// Tidy up time bound inputs
		if ($fromdatetime === NULL)
		{
			$fromdatetime = '1970-01-01 00:00:00';
		}

		if ($todatetime === NULL)
		{
			$todatetime = '3000-01-01 23:59:00';
		}

		// Get formfield id for admin1
		$formfield = ORM::factory('form_field')
							->where('field_name',$formfieldname)
							->find();
		$formfield_id = $formfield->id;

		// Get all reports in the top-level category (or all reports if category==0), 
		$table_prefix = Kohana::config('database.default.table_prefix');
		// $sql = 'SELECT '
		// 	. 'f.form_response, COUNT(f.incident_id) as count ';
		// $sql .=  'FROM '.$table_prefix.'form_response f '
		// 	. 'LEFT JOIN '.$table_prefix.'incident i ON (i.id = f.incident_id) '
		// 	. 'LEFT JOIN '.$table_prefix.'incident_category ic ON (ic.incident_id = f.incident_id) '
		// 	. 'LEFT JOIN '.$table_prefix.'category c ON (c.id = ic.category_id) ';
		// $sql .= 'WHERE (c.id = '.$category.' OR c.parent_id = '.$category.') ';
		// #Filter reports by date and active (if set)
		// $sql .= "AND i.incident_date >= STR_TO_DATE('".$fromdatetime."', '%Y-%m-%d %H:%i:%s') "
		// 	. "AND i.incident_date <= STR_TO_DATE('".$tomdatetime."', '%Y-%m-%d %H:%i:%s')".' ';
		// if ($approved)
		// {
		// 	$sql .= 'AND i.incident_active = 1 ';
		// }
		// $sql .=	'GROUP BY form_response ORDER BY count DESC ';

		$sql = 'SELECT '
			. 'f.form_response, COUNT(f.incident_id) as count '
			. 'FROM '.$table_prefix.'form_response f '
			. 'WHERE f.form_field_id = '.$formfield_id.' '
			. 'GROUP BY form_response ORDER BY count DESC ';
		$region_count_sql = Database::instance()->query($sql);
		$region_counts = array();
		foreach ($region_count_sql as $regcount) {
			$region_counts[] = [(string)$regcount->form_response, (integer)$regcount->count];
		}

		// Get total number of reports in these categories (counts might not be independant above)
		// $sql = 'SELECT COUNT(incident_id) AS count '
		// 	. 'FROM '.$table_prefix.'incident_category ic '
		// 	. 'LEFT JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) '
		// 	. 'LEFT JOIN '.$table_prefix.'incident i ON (ic.incident_id = i.id) '
		// 	. 'WHERE (c.id = '.$category.' OR c.parent_id = '.$category.') ';
		$sql = 'SELECT COUNT(id) AS count from '.$table_prefix.'form_response f '
			. 'WHERE f.form_field_id = '.$formfield_id;
		$sql_num_reports = Database::instance()->query($sql);
		$num_reports = (integer)$sql_num_reports[0]->count;

		$data = array();
		$data['counts'] = $region_counts;
		$data['vizdata'] = self::normalise_counts($region_counts, $num_reports, 100);
		$data['num_reports'] = $num_reports;
		$data['earliest_datetime'] = $fromdatetime;
		$data['latest_datetime'] = $todatetime;

		return $data;
	}

}
