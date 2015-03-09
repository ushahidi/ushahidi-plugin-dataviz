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
class DataViz_Settings_Controller extends Admin_Controller
{
 	public function index()
 	{
		$this->template->this_page = 'addons';
		
		// Settings Form View
		$this->template->content = new View("admin/addons/plugin_settings");
		$this->template->content->title = Kohana::lang('ui_dataviz.dataviz_settings');
		$this->template->content->settings_form = new View("admin/dataviz/dataviz_settings");
		$locales = ush_locale::get_i18n();

		// Setup and initialize form field names
		$form = array(
			'action' => '',
			'gisfile_id' => '',
			'gisfile_description' => '',
			'gisfile_formfields' => '',
			'gisfile_file' => '',
			'gisfile_options' => ''
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{			
			// Fetch the new input data and old stored data for this gisfile id (if it's input)
			$post_data = array_merge($_POST, $_FILES);
			echo(json_encode($post_data));
			$gisfile = (isset($post_data['gisfile_id']) AND Gisfile_Model::is_valid_gisfile($post_data['gisfile_id']))
						? new Layer_Model($post_data['gisfile_id'])
						: new Layer_Model();

			// Add / Edit data
			if ($post_data['action'] == 'a')
			{

				// Extract input data
				$gisfile_data = arr::extract($post_data, 'gisfile_description', 'gisfile_formfields', 
					'gisfile_file_old');
				$gisfile_data['gisfile_file'] = isset($post_data['gisfile_file']['name'])? $post_data['gisfile_file']['name'] : NULL;
				$gisfile_formfields = $_POST['gisfile_formfields']; //FIXIT: give user a list of existing formfields to pick form; process list here

				// Extract file data for upload validation
				$file_data = arr::extract($post_data, 'gisfile_file');
				
				// Validate: make sure we're only uploading geojson files here
				$post = Validation::factory($file_data)
						->pre_filter('trim', TRUE)
						->add_rules('gisfile_file', 'upload::valid','upload::type[json,geojson]');
				
				// Test to see if validation has passed
				if ($gisfile->validate($gisfile_data) AND $post->validate(FALSE))
				{
					// Success! SAVE
					echo("saving");
					$gisfile->save();
					
					$path_info = upload::save("gisfile_file");
					if ($path_info)
					{
						$path_parts = pathinfo($path_info);
						$file_name = $path_parts['filename'];
						$file_ext = $path_parts['extension'];
						$gisfile_filename = $file_name.".".$file_ext;

						// Resave gisfile with new filename and options list
						$gisfile->gisfile_filename = $gisfile_filename;
                		$gisfile->gisfile_options = geojson::get_geometries($gisfile_filename);
						$gisfile->save();
					}
					
					$form_saved = TRUE;
					array_fill_keys($form, '');
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
				else
				{
					// Validation failed

					// Repopulate the form fields
					$form = arr::overwrite($form, array_merge($gisfile_data->as_array(), $post->as_array()));

					// Ropulate the error fields, if any
					$errors = arr::overwrite($errors, array_merge($gisfile_data->errors('gisfile'), $post->errors('gisfile')));
					$form_error = TRUE;
				}
				
			}
			elseif ($post_data['action'] == 'd')
			{
				// Delete action
				if ($gisfile->loaded)
				{
					// Delete geotiff file if any
					$gisfile_file = $gisfile->gisfile_filename;
					if ( ! empty($gisfile_file) AND file_exists(Kohana::config('upload.directory', TRUE).$gisfile_file))
					{
						unlink(Kohana::config('upload.directory', TRUE) . $gisfile_file);
					}

					$gisfile->delete();
					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
		}

		#Gisformfield is the link between a dropdown formfield and a geojson map of a region 
		$gisfiles = ORM::factory('gisfile')
			->find_all();

		$num_gisfiles = ORM::factory('gisfile')
			->count_all();

		$this->template->content->settings_form->form = $form;
		$this->template->content->settings_form->errors = $errors;
		$this->template->content->settings_form->form_error   = $form_error;
		$this->template->content->settings_form->form_saved   = $form_saved;
		$this->template->content->settings_form->form_action  = $form_action;
		$this->template->content->settings_form->gisfiles     = $gisfiles;
		$this->template->content->settings_form->num_gisfiles = $num_gisfiles;
		$this->template->content->settings_form->locale_array = $locales;
		$this->template->form_error = $form_error;

		// Javascript Header
		$this->themes->colorpicker_enabled = TRUE;
		$this->themes->tablerowsort_enabled = TRUE;
		$this->themes->js = new View('admin/dataviz/datavizsettings_js');
		$this->themes->js->locale_array = $locales;

 	}
}