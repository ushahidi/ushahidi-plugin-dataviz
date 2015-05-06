=== About ===

name: DataViz
website: http://www.ushahidi.com
description: Add d3 visualisations to Ushahidi platform
version: 0.1
requires: 2.7
tested up to: 2.7
author: Sara Terp
author website: http://www.ushahidi.com

== Description ==

Uses D3.js to add visualizations to Ushahidi.

== Installation ==

1. Copy the entire ushahidi-plugin-dataviz directory into your /plugins/ directory.
2. Copy the files d3.js and topojson.v1.min.js into the directory media/js.
3. Activate the plugin.
4. If you want to use choropleth visualizations, you’ll need a topojson file containing the boundaries of the regions in the choropleth.  Topojson files for admin level 1 (regions) for most countries in the world are in the zipfile countries.zip; the 3-letter codes at the start of each file is the ISO3166 Alpha3 codes for that country. (Techs: these files have been generated with GDAL from the NaturalEarth.com admin1 dataset).
4a. Load the topojson by:
* Go to the dataviz plugin’s settings page (yoursite/admin/dataviz_settings)
* At the bottom of the file, add a description to the add/edit box, then select your geojson file. Your file will appear in a row in the list above; if all’s gone well, the row will also contain a list of options (e.g. “Sa`dah,Al Hudaydah,etc” for Yemen); copy that list.  
* Go to the form edit page (yoursite/admin/manage/forms). Add a dropdown box, and put the list of options for your file into its “dropdown choices” box. Copy the name of the new dropdown box.
* Go back to the dataviz plugin settings page. Click edit on the line containing your file.  Type the exact name (e.g. capitalization, punctuation etc) of the dropdown box into the “linked to formfields” box. 
* Test by creating some reports with the dropdown box filled in, then going to the data visualizations page.
5. To see data visualizations, go to yoursite/admin/dataviz (this page is also accessible via the manage then visuals menu in the admin dashboard). 

== Use ==

The dataviz plugin allows the user to visualize datasets in Ushahidi Platform 2.7.

You can currently create bar charts, column charts, line charts and choropleth maps from categories and GIS formfields (see above). 


== Extension ==

The dataviz plugin is built using D3.  Please feel free to adapt it, add your own visualizations and generally play with helping to give mappers better views of their datasets. 







