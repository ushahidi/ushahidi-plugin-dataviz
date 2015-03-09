<?php 
/**
 * Dataviz admin view page.
 * this is an adapted version of the categories view page.
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
?>

<div class="bg">
	<?php
	if ($form_error) 
	{
	?>
		<!-- red-box -->
		<div class="red-box">
		<h3><?php echo Kohana::lang('ui_main.error');?></h3>
		<ul>
			<?php
			foreach ($errors as $error_item => $error_description)
			{
				print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
			}
			?>
		</ul>
	</div>
	<?php
	}

	if ($form_saved) 
	{
	?>
		<!-- green-box -->
		<div class="green-box">
			<h3><?php echo Kohana::lang('ui_dataviz.gis_link_has_been');?> <?php echo $form_action; ?>!</h3>
		</div>
	<?php
	}
	?>
				
	<!-- tabs: the little areas that pop up when you click on e.g. 'edit' -->
	<div class="tabs">
		<!-- tabset -->
		<a name="add"></a>
		<ul class="tabset">
			<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
		</ul>
		
		<!-- add/edit gis file tab -->
		<div class="tab">
			
			<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 
				'id' => 'gisfileMain', 'name' => 'gisfileMain')); ?>
			<input type="hidden" id="gisfile_id" name="gisfile_id" value="" />
			<input type="hidden" name="action" id="action" value="a"/>
			<input type="hidden" name="gisfile_file_old" id="gisfile_file_old" value=""/>
			<div class="tab_form_item">
				<strong><?php echo Kohana::lang('ui_main.description');?>:</strong><br />
				<?php print form::input('gisfile_description', $form['gisfile_description'], ' class="text gisfile_description"'); ?>
			</div>
			<div class="tab_form_item">
				<strong><?php echo Kohana::lang('ui_dataviz.linked_to_formfields');?>:</strong><br />
				<?php print form::input('gisfile_formfields', $form['gisfile_formfields'], ' class="text gisfile_formfields"'); ?><br/>
			</div>

			<div class="tab_form_item">
				<strong><?php echo Kohana::lang('ui_dataviz.upload_geojson_file');?>:</strong><br />
				<?php print form::upload('gisfile_file', '', ''); ?>
			</div>

			<div style="clear:both"></div>
			<div class="tab_form_item">
				<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.save');?>" />
			</div>
			<?php print form::close(); ?>			
		</div>

	</div>

	<!-- report-table -->
	<div class="report-form">
		<?php print form::open(NULL,array('id' => 'gisfileListing', 'name' => 'gisfileListing')); ?>
		<input type="hidden" name="action" id="action" value="">
		<input type="hidden" name="gisfile_id" id="gisfile_id_action" value="">
		<div class="table-holder">
			<table class="table-graph generic-data">
				<tr >
					<th class="col-3"><?php echo Kohana::lang('ui_dataviz.gisfile_file');?></th>
					<th class="col-2"><?php echo Kohana::lang('ui_dataviz.gisfile_description');?></th>
					<th class="col-1"><?php echo Kohana::lang('ui_dataviz.linked_to_formfields');?></th>
					<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
				</tr>
				<?php
				if ($num_gisfiles == 0)
				{
				?>
					<tr>
						<td colspan="4" class="gdDesc">
							<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
						</td>
					</tr>
					<?php	
				}
				foreach ($gisfiles as $gisfile)
				{
					$gisfile_id          = $gisfile->id;
					$gisfile_description = $gisfile->gisfile_description;
					$gisfile_formfields  = $gisfile->gisfile_formfields;
					$gisfile_file        = $gisfile->gisfile_filename;
					$gisfile_options     = $gisfile->gisfile_options;
					?>
					<tr id="<?php echo $gisfile_id; ?>">
						<td class="col-3">
							<p><?php echo html::escape($gisfile_file); ?></p>
						</td>
						<td class="col-2">
							<div class="post">
								<h4><?php echo html::escape($gisfile_description); ?></h4>
								(Options: "<?php echo $gisfile_options; ?>")
							</div>
						</td>
						<td class="col-1">
							<p><?php echo " "; ?></p>
						</td>
						<td class="col-4">
							<ul>
								<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($gisfile_id)); ?>','<?php echo(rawurlencode($gisfile_description)); ?>','<?php echo(rawurlencode($gisfile_formfields)); ?>','<?php echo(rawurlencode($gisfile_file)); ?>')"><?php echo Kohana::lang('ui_main.edit');?></a></li>
								<li class="none-separator"><a href="javascript:gisAction('d','DELETE','<?php echo(rawurlencode($gisfile_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
							</ul>
						</td>
					</tr>
				<?php
				}
				?>
			</table>
		</div>
		<?php print form::close(); ?>
	</div>

</div>
