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

<script type="text/javascript" src="../media/js/saveSvgAsPng.js"></script>
<div class="content-wrap clearfix">
    <div>
		<?php echo form::open('admin/dataviz', array('method' => 'get', 'style' => "display: inline;")); ?>

			<div style="height:30px;margin-top:25px;">
				<span style="display:inline-block;width:80px;">Region:</span>
				<select name='region'>
				<?php
					if ($region == 0) {
						echo '<option value=0 selected>All Regions</option>';
					} else {
						echo '<option value=0>All Regions</option>';
					}
				?>

				<?php
					foreach ($region_array as $region_id => $region_title) {
						echo '<option value='.$region_id;
						if ($region == $region_id) {
							echo ' selected>';
						} else {
							echo ' >';
						}
						echo $region_title.'</option>';
					}
				?>
				</select>
			</div>

			<div style="height:30px;">
				<span style="display:inline-block;width:80px;">Variable:</span>
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

				<?php foreach ($axis_array as $category_id => $category_title)
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
			</div>

			<div style="height:30px;">
				<span style="display:inline-block;width:80px;">Date Range:</span>
				<input type="text" class="dp" name="dp1" id="dp1" value="<?php echo $dp1; ?>" />
				<input type="text" name="tp1" id="tp1" value="<?php echo $tp1; ?>" style="width:50px;" />
				&nbsp;&nbsp; to <input type="text" class="dp" name="dp2" id="dp2" value="<?php echo $dp2; ?>" />
				<input type="text" name="tp2" id="tp2" value="<?php echo $tp2; ?>" style="width:50px;" />
				<!-- <p>or click for last <a href="<?php print url::site() ?>admin/stats/reports/?range=30"><?php echo Kohana::lang('stats.time_range_1');?></a> <a href="<?php print url::site() ?>admin/stats/reports/?range=90"><?php echo Kohana::lang('stats.time_range_2');?></a> <a href="<?php print url::site() ?>admin/stats/reports/?range=180"><?php echo Kohana::lang('stats.time_range_3');?></a> <a href="<?php print url::site() ?>admin/stats/reports/"><?php echo Kohana::lang('stats.time_range_all');?></a> -->
				<input type="hidden" name="range" value="<?php echo $range; ?>" />

				<script type="text/javascript">
					$(document).ready(function() {

						// Uncomment datepicker options to use a little calendar icon next to the form fields

						$("#dp1").datepicker({
							/*showOn: "both",
							buttonImage: "<?php echo url::base(); ?>media/img/icon-calendar.gif",
							buttonImageOnly: true*/
							dateFormat: "yy-mm-dd"
						});

						$("#dp2").datepicker({
							/*showOn: "both",
							buttonImage: "<?php echo url::base(); ?>media/img/icon-calendar.gif",
							buttonImageOnly: true*/
						});
					});
				</script>
			</div>

			<div style="height:30px;">
				<span style="display:inline-block;width:80px;">Type:</span>
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
			</div>

			<div style="height:30px;">
				<input type="submit" value="Go &rarr;" class="button" />
			</div>

			<?php echo form::close(); ?>

	</div>

	<!-- Left Column: Visualisation -->
	<?php echo $reports_chart; ?>

	<?php /* This paragraph tag maintains height since the chart is positioned absolutely and will overlap the dataset otherwise */ ?>
	<p style="height:<?php echo $chartheight; ?>px;"></p>
	<div style="clear:both;"></div>


<!-- Export visualisation button: easy version -->
    <div>
    	<p>
	    	<a href="javascript:saveSvgAsPng(document.getElementById('chart-holder').lastChild, 'diagram.png');"><?php echo Kohana::lang('ui_dataviz.download_SVG');?></a>
		</p>
	</div>
	<div style="clear:both;"></div>


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


