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

$(document).ready(function() {	
	// Visualisation download buttons
	$("#save_as_svg").click(function() { submit_download_form("svg"); });
	$("#save_as_pdf").click(function() { submit_download_form("pdf"); });
	$("#save_as_png").click(function() { submit_download_form("png"); });
});


/*
   Visualisation download code
   See http://d3export.housegordon.org/ for mechanics 
   FIXIT: need to add php script to handle download - see http://d3export.housegordon.org/download.txt
   Utility function: populates the <FORM> with the SVG data
   and the requested output format, and submits the form.
*/
function submit_download_form(output_format)
{
	// Get the d3js SVG element
	var tmp = document.getElementById("chart-holder");
	var svg = tmp.getElementsByTagName("svg")[0];
	// Extract the data as SVG text string
	var svg_xml = (new XMLSerializer).serializeToString(svg);

	// Submit the <FORM> to the server.
	// The result will be an attachment file to download.
	var form = document.getElementById("printform");
	form['output_format'].value = output_format;
	form['data'].value = svg_xml;
	form.submit();
}


/*
   Clean code to print SVG contents of D3 on page
*/
function print_svg(output_format)
{
	// Get the d3js SVG element
	var tmp = document.getElementById("chart-holder");
	var svg = tmp.getElementsByTagName("svg")[0];
	// Extract the data as SVG text string
	var svg_xml = (new XMLSerializer).serializeToString(svg);

	// Submit the <FORM> to the server.
	// The result will be an attachment file to download.
	var form = document.getElementById("printsimple");
	form['print_format'].value = output_format;
	form['print_data'].value = svg_xml;
	form.submit();
}


// D3: Display barchart
function barchart(datahtml, boxwidth, boxheight)
{

	var margin = {top: 20, right: 20, bottom: 30, left: 200};
	var width  = boxwidth - margin.left - margin.right;
	var height = boxheight - margin.top - margin.bottom;

	var dataset = datahtml;
    //    dataset = dataset.sort(function(a,b) {
    //      return a[1] > b[1];
    //    });
 
	var svg = d3.select("div.bg")
		//var svg = d3.select(".chart-holder")
		.append("svg")
	    .attr("width", width + margin.left + margin.right)
	    .attr("height", height + margin.top + margin.bottom)
	    .append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var xScale = d3.scale.linear()
		.domain([0, d3.max(dataset, function(d) { 
				return d[1];
		})])
		.range([0, width]);

    var yScale = d3.scale.ordinal()
		.domain(dataset.map(function(d) {
				return d[0];
		}))
		.rangeBands([height, 0]);

		var xAxis = d3.svg.axis()
		.scale(xScale)
		.orient("bottom")
		.ticks(10, "%");

  	var yAxis = d3.svg.axis()
		.scale(yScale)
		//.attr("transform", "translate(80,0)")
		.orient("left");

		svg.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);

		svg.append("g")
		.attr("class", "y axis")
		.call(yAxis);

		svg.selectAll(".bar")
  .data(dataset)
		.enter()
		.append("rect")
		.attr("class", "bar")
		.attr("x", 0)
		.attr("width", function(d) { 
				return xScale(d[1]);
		})
		.attr("y", function(d) {
				return yScale(d[0]);
		})
  //.attr("fill", "steelblue")
		//.attr("fill", function(d, i) {
		//		return "rgb(0,0, " + (i*100) + ")";
		//})
		.attr("height", yScale.rangeBand());
}


	
	
	
