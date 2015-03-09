<?php 
/**
 * Data visualisations view page.
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
<h2>
<?php admin::manage_subtabs("Data Visualization"); ?>
</h2>

<div class="content-wrap clearfix">
    <div>
		<?php echo form::open('admin/dataviz', array('method' => 'get', 'style' => "display: inline;")); ?>
			<p><b>Step 1:</b> Pick a variable: 
			<select name='category'>
				<?php if ($category == 0)
				{
					print('<option value=0 selected>All Categories</option>');
				}
				else
				{
					print('<option value=0>All Categories</option>');
				}
				?>
						
				<?php foreach ($category_array as $category_id => $category_title)
				{
					print('<option value='.$category_id);
					if ($category == $category_id)
					{
						print(' selected>');
					}
					else
					{
						print(' >');
					}
					print($category_title.'</option>');
				}
				?>
			</select>

			<P><b>Step 2:</b> <?php echo Kohana::lang('stats.choose_date_range');?>: 
				<input type="text" class="dp" name="dp1" id="dp1" value="<?php echo $dp1; ?>" />&nbsp;&nbsp;-&nbsp;&nbsp; 
				<input type="text" name="tp1" id="tp1" value="<?php echo $tp1; ?>" />
				<input type="text" class="dp" name="dp2" id="dp2" value="<?php echo $dp2; ?>" /> 
				<input type="text" name="tp2" id="tp2" value="<?php echo $tp2; ?>" />
				<!-- <p>or click for last <a href="<?php print url::site() ?>admin/stats/reports/?range=30"><?php echo Kohana::lang('stats.time_range_1');?></a> <a href="<?php print url::site() ?>admin/stats/reports/?range=90"><?php echo Kohana::lang('stats.time_range_2');?></a> <a href="<?php print url::site() ?>admin/stats/reports/?range=180"><?php echo Kohana::lang('stats.time_range_3');?></a> <a href="<?php print url::site() ?>admin/stats/reports/"><?php echo Kohana::lang('stats.time_range_all');?></a> -->
				<input type="hidden" name="range" value="<?php echo $range; ?>" />

			<p><b>Step 3:</b> Choose a visualisation type
			<select name='viztype'>
				<?php foreach ($viztypes as $v)
				{
					print('<option value='.$v);
					if ($viztype == $v)
					{
						print(' selected>');
					}
					else
					{
						print(' >');
					}
					print($v.'</option>');
				}
				?>
			</select>

			<p><b>Step 4:</b> <input type="submit" value="Go &rarr;" class="button" />
			<?php echo form::close(); ?>
			</p>
	</div>
	
	<!-- Left Column: Visualisation -->
	<div class="chart-holder" id="chart-holder" style="height:350px;text-align:center;">
		<?php echo $reports_chart; ?>
	</div>
	<p></p>
	<div style="clear:both;"></div>


<!-- Export visualisation button: easy version -->
<!--    <div>
    	<p>
	    	<a href="javascript:print_svg('svg')" class="print"><?php echo Kohana::lang('ui_dataviz.print_as_svg');?></a>
			
			<?php echo form::open('admin/dataviz', array('method' => 'get', 'id' => 'printsimple')); ?>
				<input type="hidden" name="action" value="print" />
			 	<input type="hidden" name="print_format" id="print_format" value="">
	 			<input type="hidden" name="print_data" id="print_data" value="">
			<?php echo form::close(); ?>
		</p>
	</div>
	<div style="clear:both;"></div>
-->
 
<!-- Buttons to export visualisation to file -->
<!-- <?php echo form::open('admin/dataviz', array('method' => 'post', 'id' => 'printform', 'style' => "display: inline;")); ?>
	<input type="hidden" name="action" value="print" />
 	<input type="hidden" id="output_format" name="output_format" value="">
 	<input type="hidden" id="data" name="data" value="">
<?php echo form::close(); ?> -->

<!-- <form id="printform" method="post" action="download.pl"> 
	<input type="hidden" name="action" value="print" />
 	<input type="hidden" id="output_format" name="output_format" value="">
 	<input type="hidden" id="data" name="data" value="">
</form> -->

<!-- <div class="export">
	<br/>
	<button class="btn btn-success" id="save_as_svg" value="">
		Save as SVG</button>
	<button class="btn btn-success" id="save_as_pdf" value="">
		Save as PDF</button>
	<button class="btn btn-success" id="save_as_png" value="">
		Save as High-Res PNG</button>
</div> -->


	<!-- Left Column: Data table -->
	<div class="two-col tc-left">
		<div class="tabs">
			<!-- tabset -->
			<ul class="tabset">
				<li><a class="active">Visualisation Dataset</a></li>
			</ul>
			<div class="tab-boxes">

				<div class="tab-box active-tab" id="unique-visitors">
					<table class="table-graph generic-data">
						<tr>
							<th class="gdItem"><?php echo $axis_name; ?></th>
							<th class="gdDesc">Number of Incidents</th>
							<th class="gdDesc">% of all items</th>
						</tr>
						<?php if ($numreports == 0): ?>
							<tr>
								<td colspan="3" class="col">
									<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
								</td>
							</tr>
						<?php endif; ?>
						<?php
						foreach($report_counts as $report_count){
							?>
							<tr>
							<td class="gdDesc"><?php echo $report_count[0]; ?></td>
							<td class="gdDesc"><?php echo $report_count[1]; ?></td>
							<td class="gdDesc"><?php echo round($report_count[1]*100/$numreports,0); ?></td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>

			</div>
		</div>
	</div>

<!-- Right Column -->
<div class="two-col tc-right">
	<!-- Nothing here yet -->
</div>
<div style="clear:both;"></div>
<br /><br />
</div>


