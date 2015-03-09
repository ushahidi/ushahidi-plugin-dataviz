/**
 * Dataviz js file.
 * 
 * Javascript functions needed by the dataviz plugin.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

// Fill in tab values
function fillFields(id, gisfile_description, gisfile_formfields, gisfile_file_old)
{
	$("#gisfile_id").attr("value", decodeURIComponent(id));
	$("#gisfile_description").attr("value", decodeURIComponent(gisfile_description));
	$("#gisfile_formfields").attr("value", decodeURIComponent(gisfile_formfields));
	$("#gisfile_file_old").attr("value", decodeURIComponent(gisfile_file_old));
}

function gisAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Gisfile ID
		$("#gisfile_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#gisfileListing").submit();
	}
}