#!/usr/bin/env python
""" Create dummy Ushahidi upload data given Ushahidi site URL and csv list of possible locations

Sara-Jayne Terp
2014
"""

import requests
from requests.auth import HTTPBasicAuth
import json
import csv
from loremipsum import get_sentences, get_paragraphs
import random
import time

""" Get categories list from ushahidi site
"""
def get_categories(mapurl):
	#Put list of sites into a dictionary
	response = requests.get(url=mapurl+"api?task=categories")
	jsondata = json.loads(response.text)
	payload = jsondata['payload']['categories']
	return(payload)

#DAte1, date2 should be strings, in form "dd Mmm yy", with date2 later than date1
def create_random_datetime(date1, date2):
	epoch1 = time.mktime(time.strptime(date1, "%Y-%m-%d"));
	epoch2 = time.mktime(time.strptime(date2, "%Y-%m-%d"));
	newepoch = epoch1 + random.random()*(epoch2-epoch1);
	newtime = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(newepoch));
	# 2014-11-27 09:22:00
	return(newtime)

#Create a CSV file containing fake reports, using locations from a csv list and
#categories pulled via the site API to seed it. 

#Make list of category ids
mapurl = "https://yemenelections.ushahidi.com/";
catids = []
for cat in get_categories(mapurl):
	catids += [cat['category']['title']];

#Make list of locations: name, latitude, longitude
locs = [ \
['Ibb', 13.992356, 44.164838], \
['Ta\'izz', 13.373236, 44.033002], \
['San\'aa', 15.371458, 44.210937], \
['Hadramaut', 14.608361, 49.144142]];

#Create CSV upload file
fout = open("dummyushdata.csv", "wb")
csvout = csv.writer(fout, quoting=csv.QUOTE_NONNUMERIC)
headers = ["INCIDENT TITLE","DESCRIPTION","INCIDENT DATE", \
"LOCATION","LATITUDE", "LONGITUDE", "CATEGORY"];

csvout.writerow(headers)
for i in range(0,100):
	row = [];
	row += [get_sentences(1)]; #Incident Title
	row += [get_paragraphs(1)]; #Description
	row += [create_random_datetime("2014-01-01","2014-12-30")]; #incident date
	locpos = int(round((len(locs)-1)*random.random()));
	row += [locs[locpos][0]];#location name
	row += [locs[locpos][1]];#latitude
	row += [locs[locpos][2]];#longitude
	numcats = int(1+round((4)*random.random())); #Between 1 and 5
	catsused = []
	for i in range(0,numcats):
		catsused += [catids[int(round((len(catids)-1)*random.random()))]]
	row += [",".join(catsused)]; #category
	csvout.writerow(row)
fout.close()
