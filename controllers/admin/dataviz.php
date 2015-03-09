<?php defined('SYSPATH') or die('No direct script access.');
/**
 * DataViz Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Sara Terp <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   DataViz Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/
class DataViz_Controller extends Admin_Controller
{
 	public function index()
 	{

 		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("reports_upload"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		// Print SVG to file, if requested
		if (isset($_GET['action']))
		{
			#echo(json_encode($_GET));
			$action = $_GET['action'];
			if ($action == "print")
			{
				#echo("print requested");
				#Commented out til can get links to image magick set up
				#$im = new Image_GraphicsMagick_Driver("");
				#$im = new Imagick();
				#$im->readIMageBlob($_GET['print_data']);
				#$im->setImageFormat("jpeg");
				#$im->writeImage('/visualisation_image.jpg');
				#$im->clear();
				#$im->destroy();
			}
		}

 		//Set up view
		$this->template->content = new View('admin/dataviz/main');
		$this->template->content->title = Kohana::lang('ui_data.data_visualisation');
		$reports_chart = new d3;
		$locales = ush_locale::get_i18n();


		//get/set chart type
		$viztype = 'barchart';
		$viztypes = ["barchart", "columnchart", "linechart", "choropleth"];
		if (isset($_GET['viztype']))
		{
			$viztype = $_GET['viztype'];
		}

		//Get/set categories
		$category_array = ORM::factory('category')
			->where('parent_id','0')
			->where('category_trusted != 1')
			->select_list('id', 'category_title');	

		// $formfield_names = ORM::factory('formfield')
		// 	->where('field_type = 7') #Only get dropdown fields
		// 	->select_list('id', 'field_name');
		$formfieldname = '';
		$formfield_names = ['Admin1'];
		$formfield_ids = [-1]; #If we make the categories positive and formfields negative, we can mix them in the view list

		#Add eligible formfields to end of categories array - might want to make these the GIS fields only
		for ($i=0; $i<count($formfield_ids); $i++) {
			$category_array[$formfield_ids[$i]] = $formfield_names[$i];
		}

		if (isset($_GET['category']) and ($_GET['category'] <> 0))
		{
			$category = $_GET['category'];

			if (in_array($category, $formfield_ids)) {
				$category = 0;
				$cats = Category_Model::categories();
				$formfieldname = $formfield_names[array_search($category, $formfield_ids)];
				$axis_name = "GIS Region";
			}

			else {
				$category_details = ORM::factory('category')->where('id', $category)->find();
				$axis_name = $category_details->category_title;

				// Match the wierdass thing that the categories model puts out
				$catrows = ORM::factory('category')->where('parent_id',$category)->find_all();
				
				$cats = array();
				foreach($catrows as $catrow)
				{
					$cats[$catrow->id]['category_id'] = $catrow->id;
					$cats[$catrow->id]['category_title'] = $catrow->category_title;
					$cats[$catrow->id]['category_description'] = $catrow->category_description;
					$cats[$catrow->id]['category_color'] = $catrow->category_color;
					$cats[$catrow->id]['category_image'] = $catrow->category_image;
					$cats[$catrow->id]['category_image_thumb'] = $catrow->category_image_thumb;
				}
			}
		}
		else
		{
			$category = 0;
			$axis_name = "All Categories";
			$cats = Category_Model::categories();
		}


		//get/set time ranges
		$range = 10000;
		$dp1 = "1970-01-01";
		$tp1 = "00:00";
		if (isset($_GET['dp1']))
		{
			$dp1 = $_GET['dp1'];
			preg_match("/(\d{2}):(\d{2})/", $_GET['tp1'], $hoursmins);
			if (sizeof($hoursmins) > 1)
			{
				if (($hoursmins[0] < 24) and ($hoursmins[1] < 60))
				{
					$tp1 = 	$_GET['tp1'];
				}
			}
		}
		$fromdatetime = date( "Y-m-d H:i:s", strtotime($dp1 . " " . $tp1 . ":00"));

		$dp2 = "3000-01-01";
		$tp2 = "23:59";
		if (isset($_GET['dp2']))
		{
			$dp2 = $_GET['dp2'];
			$tp2 = "00:00";
			preg_match("/(\d{2}):(\d{2})/", $_GET['tp2'], $hoursmins);
			if (sizeof($hoursmins) > 1)
			{
				if (($hoursmins[0] < 24) and ($hoursmins[1] < 60))
				{
					$tp2 = $_GET['tp2'];
				}
			}
		}
		$todatetime = date( "Y-m-d H:i:s", strtotime($dp2 . " " . $tp2 . ":00"));
	
		// Get dataset for visualisation
		if ($viztype == "linechart") {
			$dataset = Dataviz_Model::get_counts_by_date($fromdatetime, $todatetime, $category, $formfieldname, true);
		}
		elseif (($formfieldname != '') or ($viztype == "choropleth")) {
			if (($formfieldname == '') and ($viztype == "choropleth")) {
				$formfieldname = 'Admin1'; #FIXIT: nasty hack to stop user getting frustrated for now
			}
			$dataset = Dataviz_Model::get_region_counts($fromdatetime, $todatetime, $category, $formfieldname, true);
		}
		else {
			$dataset = Dataviz_Model::get_category_counts($fromdatetime, $todatetime, $category, $formfieldname, true);
		}

		# Create visualisation html
		$colors = array();
		$options = array();
		$chartwidth = 900;
		$chartheight = 350;
		if ($viztype == "linechart") {
			$reports_chart = $reports_chart->linechart('reports',$dataset['vizdata'],$options,$colors,$chartwidth,$chartheight);
		}
		elseif ($viztype == "choropleth") {
			$reports_chart = $reports_chart->choropleth('reports',$dataset['vizdata'],$options,$colors,$chartwidth,$chartheight);
		}
		elseif ($viztype == "columnchart") {
			$reports_chart = $reports_chart->colchart('reports',$dataset['vizdata'],$options,$colors,$chartwidth,$chartheight);
		}
		else { //default to barchart
			$reports_chart = $reports_chart->barchart('reports',$dataset['vizdata'],$options,$colors,$chartwidth,$chartheight);
		}

		// Set up view
		$this->template->content->viztype = $viztype;
		$this->template->content->viztypes = $viztypes;
		$this->template->content->category = $category;
		$this->template->content->axis_name = $axis_name;
		$this->template->content->category_array = $category_array;			
        $this->template->content->dp1 = $dp1;
        $this->template->content->tp1 = $tp1;
        $this->template->content->dp2 = $dp2;
        $this->template->content->tp2 = $tp2;
		$this->template->content->range = $range;
		$this->template->content->numreports = $dataset['num_reports'];
		$this->template->content->report_counts = $dataset['counts'];
 		$this->template->content->reports_chart = $reports_chart;

		// Javascript Header
		$locales = ush_locale::get_i18n();
		$this->themes->colorpicker_enabled = TRUE;
		$this->themes->tablerowsort_enabled = TRUE;
		$this->themes->js = new View('admin/dataviz/dataviz_js');
		$this->themes->js->locale_array = $locales;
	}
}