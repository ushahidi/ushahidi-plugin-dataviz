<?php

class d3 {
	
	function d3() {
	
	}

	/*
	 * The chart function creates a chart
	 * 
	 * name: The name of the div element for the chart. Should be unique from all other charts
	 * data: Multi-dimensional array. Ex: array('label1'=>array(1,2,3,4,5),'label2'=>array(3,2,4,2))
	 * options: Multi-dimensional array. Ex: array('bars'=>array('show'=>'true'))
	 *          See protochart site for more details related to options: http://www.deensoft.com/lab/protochart/
	 *          Eaxample bar graph options: array('bars'=>array('show'=>'true'));
	 * custom_color: array with label as key and a RRGGBB code (ex: FF0000) as a value.
	 * width: width of the chart in pixels
	 * height: height of the chart in pixels
	 *
	 */
  public function linechart($name='chart',$data,$options_array=null,$custom_color=null,$width=400,$height=300) {
    
    //var_dump($data);
    //asort($data);

    $datarows = [];
    foreach($data as $row){
      $datarows[] = "[\"".$row[0]."\",".$row[1]."]";
    }
    $datahtml = "[".implode(",", $datarows)."];";

    $html = '<style>

      .axis path, 
      .axis line {
        fill: none;
        stroke: black;
        stroke-width: 2px;
        shape-rendering: crispEdges;
      }

      .axis text {
        font-family: sans-serif;
        font-size: 20px;
      }

      .line {
      fill: none;
      stroke-width: 5px;
      }

      .line:hover {
        stroke: brown;
      }   

      .horizontalGrid {
        fill: none;
        shape-rendering: crispEdges;
        stroke: black;
        stroke-width: 1px;
      }
    </style>
    <script type="text/javascript" src="../media/js/d3.js"></script>
    <script type="text/javascript">
     var margin = {top: 20, right: 20, bottom: 120, left: 40};
     var width = '.$width.' - margin.left - margin.right;
     var height = '.$height.' - margin.top - margin.bottom;

     var dataset = '.$datahtml.';
     //var dataset = [["2015-02-20",10], ["2015-02-21", 20], ["2015-02-22", 15]];
 
     var svg = d3.select(".chart-holder")
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

      //--------------------------------------------------
      //Set up scales and axes
      //--------------------------------------------------
      var xScale = d3.scale.ordinal()
        .domain(dataset.map(function(d) {
          return d[0];
        }))
        .rangeRoundBands([0, width]);

      var yScale = d3.scale.linear()
        .domain([0, d3.max(dataset, function(d) { 
          return d[1];
        })])
        .rangeRound([height, 0]);

      var xAxis = d3.svg.axis()
        .scale(xScale)
        .orient("bottom")
        .ticks(10, "");

      var yAxis = d3.svg.axis()
        .scale(yScale)
        //.attr("transform", "translate(80,0)")
        .orient("left");

      svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis)
        .selectAll("text")  
            .style("text-anchor", "end")
            .attr("dx", "-.8em")
            .attr("dy", ".15em")
            .attr("transform", function(d) {
                return "rotate(-65)" 
                });

      svg.append("g")
        .attr("class", "y axis")
        .call(yAxis);

      //--------------------------------------------------
      // Plot data
      //--------------------------------------------------
      var lineGen = d3.svg.line()
        //.interpolate("cardinal") //Smoothes out the line a bit
      .x(function(d) {
       return xScale(d[0]) + xScale.rangeBand()/2;
    })
      .y(function(d) {
        return yScale(d[1]);
      });

      svg.append("svg:path")
        .attr("d", lineGen(dataset))
        .attr("stroke", "steelblue")
          .attr("class", "line");

      svg.selectAll("dot")                                    
        .data(dataset)                                            
        .enter().append("circle")                                
        .attr("fill", "steelblue")
        .attr("r", 5)    
        .attr("cx", function(d) { return xScale(d[0]) + xScale.rangeBand()/2; })         
        .attr("cy", function(d) { return yScale(d[1]); })

      svg.selectAll("line.horizontalGrid")
        .data(yScale.ticks(10))
        .enter()
        .append("line")
        .attr({
          "class":"horizontalGrid",
          "x1" : margin.right,
          "x2" : width,
          "y1" : function(d){ return yScale(d);},
          "y2" : function(d){ return yScale(d);}
        });

    </script>';

     return $html; 
  }


	public function barchart($name='chart',$data,$options_array=null,$custom_color=null,$width=400,$height=300) {
		
		//var_dump($data);
    //asort($data);

    // Switches
    $responsive_axis = "true"; //x axis goes from 0 to 100 if false; 0 to max($data) if true
    $barsize = 0.5; //Size of bars relative to width available, e.g. "0.5" = bars are half-sized.
    $barsize = min($barsize, 1);
    $axistext = "%";

		$datarows = [];
		foreach($data as $row){
			$datarows[] = "[\"".$row[0]."\",".$row[1]."]";
		}
		$datahtml = "[".implode(",", $datarows)."]";

		$html = '<style>
      		.axis path, 
      		.axis line {
        		fill: none;
        		stroke: black;
       			shape-rendering: crispEdges;
      		}

	      	.axis text {
        		font-family: sans-serif;
        		font-size: 11px;
      		}

        .bar {
           fill: steelblue;
           stroke: white;
        }
        .bartext {
          font-family: sans-serif;
          font-size: 11px;
          fill: white;          
        }
		    .bar:hover {
        		fill: brown;
      		}
		    </style>
  			<script type="text/javascript" src="../media/js/d3.js"></script>
			  <script type="text/javascript">

		    	var margin = {top: 20, right: 20, bottom: 30, left: 200};
		    	var width = '.$width.' - margin.left - margin.right;
    		 	var height = '.$height.' - margin.top - margin.bottom;

		      var dataset = '.$datahtml.';
         //    dataset = dataset.sort(function(a,b) {
         //      return a[1] > b[1];
         //    });
 
	      		var svg = d3.select(".chart-holder")
        			.append("svg")
    			    .attr("width", width + margin.left + margin.right)
    			    .attr("height", height + margin.top + margin.bottom)
    			    .append("g")
    			    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

          if ('.$responsive_axis.' == true)
          {
      			var xScale = d3.scale.linear()
          			.domain([0, d3.max(dataset, function(d) { 
            				return d[1];
          			})])
          			.range([0, width]);
          }
          else
          {
            var xScale = d3.scale.linear()
                .domain([0, 100])
                .range([0, width]);
          }

			    var yScale = d3.scale.ordinal()
        			.domain(dataset.map(function(d) {
          				return d[0];
        			}))
        			.rangeBands([height, 0]);

      			var xAxis = d3.svg.axis()
        			.scale(xScale)
        			.orient("bottom")
        			.ticks(10)
              .tickFormat(function(d) { 
                return d + "'.$axistext.'"; 
              });

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
          				return yScale(d[0]) + yScale.rangeBand()*(1-'. $barsize.')/2;
        			})
              //.attr("fill", "steelblue")
        			//.attr("fill", function(d, i) {
          		//		return "rgb(0,0, " + (i*100) + ")";
        			//})
        			.attr("height", yScale.rangeBand()*'.$barsize.');

            svg.selectAll("bartext")
              .data(dataset)
              .enter()
              .append("text")
              .attr("text-anchor", "right")
              .attr("class", "bartext")
              .text(function(d) {
                return(d[1]);
              })
               .attr("x", function(d) {
                return xScale(d[1]) - 35;
              })
              .attr("y", function(d) {
                return yScale(d[0]) + yScale.rangeBand()*1/2;
              });
		  	</script>';

		return $html;
	}


public function colchart($name='chart',$data,$options_array=null,$custom_color=null,$width=400,$height=300) {
    
    //var_dump($data);
    //arsort($data);

    // Switches
    $responsive_axis = "true"; //y axis goes from 0 to 100 if false; 0 to max($data) if true
    $barsize = 0.5; //Size of bars relative to width available, e.g. "0.5" = bars are half-sized.
    $barsize = min($barsize, 1);
    $axistext = "%";

    $datarows = [];
    foreach($data as $row){
      $datarows[] = "[\"".$row[0]."\",".$row[1]."]";
    }
    $datahtml = "[".implode(",", $datarows)."];";

    $html = '<style>
          .axis path, 
          .axis line {
            fill: none;
            stroke: black;
            shape-rendering: crispEdges;
          }

          .axis text {
            font-family: sans-serif;
            font-size: 11px;
          }

        .bar {
           fill: steelblue;
           stroke: white;
        }
        .bar:hover {
            fill: brown;
        }

        .bartext {
          font-family: sans-serif;
          font-size: 11px;
          fill: white;          
        }
        </style>
        <script type="text/javascript" src="../media/js/d3.js"></script>
        <script type="text/javascript">

          var margin = {top: 20, right: 20, bottom: 50, left: 50};
          var width = '.$width.' - margin.left - margin.right;
          var height = '.$height.' - margin.top - margin.bottom;

            var dataset = '.$datahtml.';
 
            var xScale = d3.scale.ordinal()
              .domain(dataset.map(function(d) {
                  return d[0];
              }))
              .rangeBands([0, width]);

          if ('.$responsive_axis.' == true)
          {
              var yScale = d3.scale.linear()
              .domain([0, d3.max(dataset, function(d) { 
                  return d[1];
              })])
              .range([height,0]);
          }
          else
          {
              var yScale = d3.scale.linear()
              .domain([0, 1])
              .range([height,0]);            
          }

            var xAxis = d3.svg.axis()
              .scale(xScale)
              .orient("bottom");

            var yAxis = d3.svg.axis()
              .scale(yScale)
              //.attr("transform", "translate(80,0)")
              .orient("left")
              .ticks(10)
              .tickFormat(function(d) { 
                return d + "'.$axistext.'"; 
              });

            var svg = d3.select(".chart-holder")
              .append("svg")
              .attr("width", width + margin.left + margin.right)
              .attr("height", height + margin.top + margin.bottom)
              .append("g")
              .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            svg.append("g")
              .attr("class", "x axis")
              .attr("transform", "translate(0," + height + ")")
              .call(xAxis)
              .selectAll(".tick text")
              .call(wrap, xScale.rangeBand());

            svg.append("g")
              .attr("class", "y axis")
              .call(yAxis);

            svg.selectAll(".bar")
              .data(dataset)
              .enter()
              .append("rect")
              .attr("class", "bar")
              .attr("x", function(d) { 
                   return xScale(d[0]) + xScale.rangeBand()*(1-'. $barsize.')/2;
              })
              .attr("width", xScale.rangeBand()*'.$barsize.')
              .attr("y", function(d) {
                  return yScale(d[1]);
              })
              .attr("height", function(d) {
                return height - yScale(d[1]);
              });

              svg.selectAll("bartext")
                .data(dataset)
                .enter()
                .append("text")
                .attr("class", "bartext")
                .attr("text-anchor", "middle")
                .text(function(d) {
                  return(d[1]);
                })
                 .attr("x", function(d) {
                  return xScale(d[0]) + xScale.rangeBand()*1/2;
                })
                .attr("y", function(d) {
                  return yScale(d[1]) + 15;
                });

              // From http://bl.ocks.org/mbostock/7555321
              function wrap(text, width) {
                text.each(function() {
                  var text = d3.select(this),
                      words = text.text().split(/\s+/).reverse(),
                      word,
                      line = [],
                      lineNumber = 0,
                      lineHeight = 1.1, // ems
                      y = text.attr("y"),
                      dy = parseFloat(text.attr("dy")),
                      tspan = text.text(null).append("tspan").attr("x", 0).attr("y", y).attr("dy", dy + "em");
                  while (word = words.pop()) {
                    line.push(word);
                    tspan.text(line.join(" "));
                    if (tspan.node().getComputedTextLength() > width) {
                      line.pop();
                      tspan.text(line.join(" "));
                      line = [word];
                      tspan = text.append("tspan").attr("x", 0).attr("y", y).attr("dy", ++lineNumber * lineHeight + dy + "em").text(word);
                    }
                  }
                });
              }

        </script>';

    return $html;
  }


  public function choropleth($name='chart',$data,$options_array=null,$custom_color=null,$width=400,$height=300) {
    
    // var_dump($data);
    
    //Input variables per country
    $countrycode = "yem";
    $uploadfile = url::base() .'media/d3maps/'.$countrycode."_admin1.json";
    $countrycenter = [48, 15.333];
    $countrysize = 2500;//4000;
    // $data = [["Sa`dah",0],["Al Hudaydah",.10],["Al Mahwit",.15],["Dhamar",.07],
    // ["Hajjah",.20],["Amran",.17],["Ibb",.25],["Lahij",.15],["Ta`izz",.04],["Al Mahrah",.15],
    // ["Al Bayda'",.17],["Al Dali'",.05],["Al Jawf",.15],["Shabwah",.21],["Ma'rib",.06],
    // ["Sana'a",.14],["Hadramawt",.10],["Amanat Al Asimah",.20],["Raymah",.03],["`Adan",.07],
    // ["Abyan",.04]];
    $maxcolour = 1; //Assume we're working with percentage values - is easier!

    $datarows = [];
    foreach($data as $row){
      $datarows[] = "[\"".$row[0]."\",".$row[1]."]";
    }
    $datahtml = "[".implode(",", $datarows)."];";

    $html = "
      <style>
        .q0-9 { fill:rgb(247,251,255); }
        .q1-9 { fill:rgb(222,235,247); }
        .q2-9 { fill:rgb(198,219,239); }
        .q3-9 { fill:rgb(158,202,225); }
        .q4-9 { fill:rgb(107,174,214); }
        .q5-9 { fill:rgb(66,146,198); }
        .q6-9 { fill:rgb(33,113,181); }
        .q7-9 { fill:rgb(8,81,156); }
        .q8-9 { fill:rgb(8,48,107); }

        .region:hover {
            stroke: rgba(0, 0, 0, 0.8);
            stroke-width: 2px;
          }

        td, th {
          padding: 1px 4px;
          border: 1px black solid;
        }
        .d3-tip {
          line-height: 1;
          font-weight: bold;
          padding: 12px;
          background: rgba(0, 0, 0, 0.8);
          color: white;
          border-radius: 2px;
          pointer-events: none;
        }
        .d3-tip .highlight {
          color: #fc4b52;
        }
        .legend {
          font-size: 12px;
        }
      </style>
      <script type='text/javascript' src='../media/js/d3.js'></script>
      <script src='../media/js/topojson.v1.min.js'></script>
      <script src='../media/js/d3-tip.js'></script>
      <script type='text/javascript'>

        var width = ".$width.";
        var height = ".$height.";

        //Set mapping from values to colours
        var quantize = d3.scale.quantize()
            .domain([0, ".$maxcolour."])
            .range(d3.range(9).map(function(i) { return 'q' + i + '-9'; }));

        //Get dataset into a form D3 can read
        dataset_in = ".$datahtml.";
        var dataset = d3.map();
        for (var i in dataset_in) { 
          dataset.set(dataset_in[i][0], dataset_in[i][1]); 
        }

        //initialise the tooltip
        var tooltip = d3.tip()
          .attr('class', 'd3-tip')
          .html(function(d){
            return '<strong>' + d.id + '</strong> <span class=\'highlight\'>' + dataset.get(d.id) + ' Incidents</span>';
          })

        var svg = d3.select('.chart-holder')
          .append('svg')
          .attr('width', width)
          .attr('height', height);
        
        //invoke tooltip for this context
        svg.call(tooltip);

        var projection = d3.geo.mercator()
          .center([".$countrycenter[0].",".$countrycenter[1]."])
          .scale(".$countrysize.")
          .translate([width/2, height/2]);

        var path = d3.geo.path()
          .projection(projection);

        d3.json('".$uploadfile."', function (error, topology) {
          if (error) return console.error(error);
          var subunits = topojson.feature(topology, topology.objects.subunits);

          svg.append('path')
            .datum(subunits)
            .attr('d', path);

          svg.selectAll('.subunit')
              .data(topojson.feature(topology, topology.objects.subunits).features)
              .enter().append('path')
              .attr('class', function(d) {
                return quantize(dataset.get(d.id))
              })
              .style('cursor', 'pointer')
              .classed('region', 'true')
              .attr('d', path)
              .on('mouseover', tooltip.show)
              .on('mouseout', tooltip.hide)
            ;
        });

    var legend = svg.selectAll('g.legend')
      .data(quantize.range().reverse())
      .enter()
      .append('g')
      .attr('class', 'legend');

    var ls_w = 20, ls_h = 20;

    legend.append('rect')
      .attr('x', 20)
      .attr('y', function(d, i){ 
        return height - (i*ls_h) - 2*ls_h;
      })
      .attr('width', ls_w)
      .attr('height', ls_h)
      .style('stroke', 'black')
      .style('stroke-width', 1)
      .attr('class', function(d) { 
        var extent = quantize.invertExtent(d);
        return quantize(extent[0]); 
      });

    legend.append('text')
      .attr('x', 50)
      .attr('y', function(d, i){ 
        return height - (i*ls_h) - ls_h - 4;
      })
      .text(function(d){ 
        var extent = quantize.invertExtent(d);
        var format = d3.format('0.2f');
        return format(+extent[0]);
      });

    </script>
    ";

     return $html;
  }

}
?>
