Page.field_map = {"year":"%D","title":"%T","journal":"%J","volume":"%V","number":"%N","pages":"%P","publisher":"%I","location":"%C","booktitle":"%B", "url":"%U"};
Page.pubtypes_json = 
				{
					"article": 
						{
							"apa_required_fields" : ['title','journal','year','volume','pages'],
							"mla_required_fields" : ['title','journal','year','volume','pages'],
							"bibtex_required_fields" : ['title','journal','year','volume','pages'],
							"fields": ['title','journal','year','volume','number','pages','month','note','key','url','doi'],
							"endnote_name":"Journal Article",
							"dropdown_name":"Article"
						},
					"book":  
						{ 
							"apa_required_fields" : ['title','publisher','year','address','location'], 										
							"mla_required_fields" : ['title','publisher','year','address','location'],										
							"bibtex_required_fields" : ['title','publisher','year','volume','series','address','location'],
							"fields": ['title','publisher','year','volume','series','address','location','edition','month','note','key','url','doi'],
							"endnote_name":"Book",
							"dropdown_name":"Book"
						},
						"edited_book": 
						{
							"apa_required_fields" : ['title','publisher','year','address','location'],			//,'editor'
							"mla_required_fields" : ['title','publisher','year','address','location'],			//,'editor'
							"bibtex_required_fields" : ['title','publisher','year','address','location'],
							"fields": ['title','publisher','year','volume','series','address','location','edition','month','note','key','url','doi'],
							"endnote_name":"Book",
							"dropdown_name":"Edited Book"
						},
					"conference":  
						{
							"apa_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location',],
							"mla_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location',],
							"bibtex_required_fields" : ['title','booktitle','year','editor','pages','organization','publisher','address','location',],
							"fields": ['title','booktitle','year','editor','pages','organization','publisher','address','location','month','note','key','url','doi'],
							"endnote_name":"Conference Paper",
							"dropdown_name":"Conference"
						},
					"inbook": 
						{
							"apa_required_fields" : ['title','booktitle','pages','publisher','year','editor','address','location'],		
							"mla_required_fields" : ['title','booktitle','pages','publisher','year','editor','address','location'],			
							"fields": ['title','booktitle','chapter','pages','publisher','year','editor','volume','series','address','location','edition','month','note','key','url','doi'],
							"endnote_name":"Book Section",
							"dropdown_name":"In Book"
						},
					"incollection": 
						{
							"apa_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location',],
							"mla_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location',],
							"bibtex_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location',],
							"fields": ['title','booktitle','year','editor','pages','organization','publisher','address','location','month','note','key','url','doi'],
							"endnote_name":"Generic",
							"dropdown_name":"In Collection"
						},
					"inproceedings": 
						{
							"apa_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location'],
							"mla_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location'],
							"bibtex_required_fields" : ['title','booktitle','year','editor','pages','publisher','address','location'],
							"fields": ['title','booktitle','year','editor','pages','organization','publisher','address','location','month','note','key','url','doi'],
							"endnote_name":"Conference Paper",
							"dropdown_name":"In Proceedings"
						},
					"manual": 
						{
							"apa_required_fields" : ['title','organization','edition','address','location','year'],
							"mla_required_fields" : ['title','organization','edition','address','location','year'],
							"bibtex_required_fields" : ['title','organization','address','location','year'],
							"fields": ['title','organization','address','location','edition','month','year','note','key','url','doi'],
							"endnote_name":"Generic",
							"dropdown_name":"Manual"
						},
					"mastersthesis": 
						{
							"apa_required_fields" : ['title','school','year'], 							
							"mla_required_fields" : ['title','school','year'],							
							"bibtex_required_fields" : ['title','school','year','address','location'],
							"fields": ['title','school','year','address','location','month','note','key','url','doi'],
							"endnote_name":"Thesis",
							"dropdown_name":"Master's Thesis"
						},
					"phdthesis": 
						{
							"apa_required_fields" : ['title','school','year'],							
							"mla_required_fields" : ['title','school','year'],							
							"bibtex_required_fields" : ['title','school','year','address','location'],
							"fields": ['title','school','year','address','location','month','note','key','url','doi'],
							"endnote_name":"Thesis",
							"dropdown_name":"Ph.D. Thesis"
						},
					"proceedings": 
						{
							"apa_required_fields" : ['title','year','editor','publisher','address','location'],
							"mla_required_fields" : ['title','year','editor','publisher','address','location'],
							"bibtex_required_fields" : ['title','year','editor','publisher','address','location'],
							"fields": ['title','year','editor','publisher','organization','address','location','month','note','key','url','doi'],
							"endnote_name":"Conference Proceedings",
							"dropdown_name":"Proceedings"
						},
					"techreport": 
						{
							"apa_required_fields" : ['title','institution','year','type','number','address','location',],
							"mla_required_fields" : ['title','institution','year','type','number','address','location',],
							"bibtex_required_fields" : ['title','institution','year','type','number','address','location',],
							"fields": ['title','institution','year','type','number','address','location','month','note','key','url','doi'],
							"endnote_name":"Generic",
							"dropdown_name":"Tech Report"
						},
					"unpublished": 
						{
							"apa_required_fields" : ['title','note','month','year'],
							"mla_required_fields" : ['title','note','month','year'],
							"bibtex_required_fields" : ['title','note','month','year'],
							"fields": ['title','note','month','year','key','url','doi'],
							"endnote_name":"Unpublished Work",
							"dropdown_name":"Unpublished"
						},
					"misc": 
						{
							"apa_required_fields" : ['title','howpublished','year'],
							"mla_required_fields" : ['title','howpublished','year'],
							"bibtex_required_fields" : ['title','howpublished','year'],
							"fields": ['title','howpublished','month','year','note','key','url','doi'],
							"endnote_name":"Generic",
							"dropdown_name":"Miscellaneous"
						},
					"translated_book": 
						{
							"apa_required_fields" : ['title','publisher','year','translator','address','location'],
							"mla_required_fields" : ['title','publisher','year','translator','address','location'],
							"bibtex_required_fields" : ['title','publisher','year','translator','address','location'],
							"fields": ['title','publisher','year','translator','volume','series','address','location','edition','month','note','key','url','doi'],
							"endnote_name":"Book",
							"dropdown_name":"Translated Book"
						},
					"web_published": 
						{
							"apa_required_fields" : ['title','year','url','date_retrieved'],		
							"mla_required_fields" : ['title','year','url','date_retrieved'],		
							"bibtex_required_fields" : ['title','year','url','date_retrieved'],
							"fields": ['title','year','date_retrieved','note','key','url','doi'],
							"endnote_name":"Web Published",
							"dropdown_name":"Web Published"
						}
						
				}		
				
Page.fields_arr =	{	
						"editor":["Editor",40],
						"volume":["Volume",15],
						"series":["Series",15],
						"location":["City",60],  // address
						"edition":["Edition",15],
						"month":["Month",15],
						"publisher":["Publisher",31],
						//"location":["Location",40],
						//"address":["Location",40],
						"number":["Number",15],
						"pages":["Pages",15],
						"booktitle":["Book Title",50],
						"chapter":["Chapter",15],
						"journal":["Journal",31],
						"crossref":["Crossref",15],
						"institution":["Institution",40],
						"organization":["Organization",40],
						"school":["School",40],
						"howpublished":["How Published",15],
						"translator":["Translator",40],
						"date_retrieved":["Date Retrieved",40]
					}

Page.author_and_constant_fields_arr = {
	"author0ln":"Author 1",	
	"author1ln":"Author 2",
	"author2ln":"Author 3",
	"author3ln":"Author 4",
	"author4ln":"Author 5",
	"author5ln":"Author 6",
	"title":"Title",
	"year":"Year"
}


// ********* BIBTEX ************
Page.printBibtexStyleCitation = function(_citation)
{
	var html = "@";
	var pubtype = Page.checkPubtype(_citation.pubtype);
		
	html += pubtype+"{"+_citation.citation_id+",<br>";
	html += "&nbsp;&nbsp;";
	
	if (pubtype == "edited_book")
	{
		html += "editor";
	}
	else
	{
		html += "author";
	}
	html += " = &#34;"+ Page.printBibtexStyleAuthors(_citation) +"&#34;,<br>";
	for(var i in Page.pubtypes_json[pubtype].fields)
	{		
		if(_citation[Page.pubtypes_json[pubtype].fields[i]] == undefined || _citation[Page.pubtypes_json[pubtype].fields[i]] == "") {
			//html += "&nbsp;&nbsp;"+Page.pubtypes_json[pubtype].fields[i]+" = &#34;&#34;,<br>";
		}
		else {
			html += "&nbsp;&nbsp;"+Page.pubtypes_json[pubtype].fields[i]+" = &#34;" + _citation[Page.pubtypes_json[pubtype].fields[i]] + "&#34;,<br>";
		}
	}
	html = html.substring(0, html.length-5);
	html += "<br>}";
	return html;
}

Page.printBibtexStyleAuthors = function(_citation)
{
	var html = "";
	
	html += _citation.author0fn + " " + _citation.author0ln + "";
	for (var j = 1; j < 6; j++) {	
		var ln = "author" + j + "ln"; // tempVar
		var fn = "author" + j + "fn";

		if ((_citation[ln] != "") && (_citation[ln] != "undefined"))
		{
			var next_ln = "author" + (j+1) + "ln"; // tempVar
			var next_fn = "author" + (j+1) + "fn";
			if((_citation[next_ln] != "") && (_citation[next_ln] != "undefined"))
			{
				html += " and "+ _citation[fn] + " " +  _citation[ln] + "";
			}
			else
			{
				html += " and "+_citation[fn] + " " +  _citation[ln] + "";	
			}
		}
	}
	return html;
}

Page.checkPubtype = function(pubtype)
{
	var found = 0;
	for (var i in Page.pubtypes_json)
	{
		if (pubtype == i) 
		{
			found = 1;
		}	
	}
	if (found == 0)
	{
		pubtype = 'misc';	
	}
	return pubtype;
}

// ********* ENDNOTE ************
Page.printEndnoteStyleCitation = function(_citation)  // http://www.cardiff.ac.uk/insrv/educationandtraining/guides/endnote/endnote_codes.html
{
	var html = "";
	var pubtype = Page.checkPubtype(_citation.pubtype);
	
	html += "%0 "+Page.pubtypes_json[pubtype].endnote_name+"<br>\n";
	html += Page.printEndnoteStyleAuthors(_citation);
	for(var i in Page.pubtypes_json[pubtype].fields)
	{	
		if(Page.pubtypes_json[pubtype].fields[i] == "editor") {
			html += Page.printEndnoteStyleEditors(_citation);
		}
		else if(_citation[Page.pubtypes_json[pubtype].fields[i]] != "" && _citation[Page.pubtypes_json[pubtype].fields[i]] != undefined) {
			html += Page.field_map[Page.pubtypes_json[pubtype].fields[i]] + " " + _citation[Page.pubtypes_json[pubtype].fields[i]] +"<br>\n";
		}
		else {}
	}
	
	return html;
}

Page.printEndnoteStyleAuthors = function(_citation)
{
	var html = "";
	for(var i = 0; i < 6; i++)
	{
		var ln = "author"+i+"ln";
		var fn = "author"+i+"fn";
			
		if(_citation[ln] != "" && _citation[fn] != "")
		{
			html += "%A "+(_citation[ln])+", "+(_citation[fn])+"<br>\n";
		}
	}
	return html;
}

Page.printEndnoteStyleEditors = function(_citation)
{
	var html = "";
	var explode;
	if(_citation.editor != undefined)
	{
		explode = _citation.editor.split(','); 
	}
	else
	{
		return html;	
	}
	
	// explode.length - 2 is to exclude the last two cells in the array. 2 cells = 1 name. 
	for(var i = 0; i < explode.length - 2; i = i + 2)
	{
		if(_citation.editor != "" && _citation.editor != undefined) html += "%E "+ explode[i]+", "+explode[i+1]+"<br>\n";
	}
	return html;
}

// ********* APA ************
Page.printAPAStyleAuthors = function(_citation) {
	
	var auth_count = 1;
	var html = "";
	
	if ((_citation.author0ln != "") && (_citation.author0ln != "undefined")){
		if((_citation.author0fn != "") && (_citation.author0fn != "undefined")){
			html += _citation.author0ln + ", " + rtrim(Page.getInitials("" + _citation.author0fn));
		}
		else
		{
			html += _citation.author0ln;//'[AUTHOR 1 NAME]';
		}
	}
	else
	{
		html += _citation.author0ln;//'[AUTHOR 1 NAME]';
	}
	
	for (var j = 1; j < 6; j++) {	
		var ln = "author" + j + "ln"; // tempVar
		var fn = "author" + j + "fn";


		if ((_citation[ln] != "") && (_citation[ln] != "undefined"))
		{
			auth_count++;
			var next_ln = "author" + (j+1) + "ln"; // tempVar
			var next_fn = "author" + (j+1) + "fn";
      
      if (auth_count > 3) {
        html = _citation.author0ln + ", " + rtrim(Page.getInitials("" + _citation.author0fn)) + ", et al. ";
        return html;
      }


			if((_citation[next_ln] == "") && (_citation[next_ln] == "undefined"))
			{
				html = rtrim(html);
				html += ", "+ _citation[ln] + ", " +  rtrim(Page.getInitials(_citation[fn])) + "";
			}
			if (auth_count == 3) 
			{
       // alert( _citation.author0ln + ", " + rtrim(Page.getInitials("" + _citation.author0fn)) + _citation.author1ln + ", " + rtrim(Page.getInitials("" + _citation.author1fn)) + " & " _citation.author2ln + ", " + rtrim(Page.getInitials("" + _citation.author2fn))); 
			 // return html;
      }
		}
	}

	
	if(_citation.pubtype == "edited_book")
	{
		if(auth_count > 1)
		{
			return html + " (Eds.). ";
		}
		else
		{
			return html + " (Ed.). ";	
		}
	}
	else
	{
		return html;	
	}
}

Page.getInitials = function(str) {


	var html = "";
	var temp = str.split(" ");
	for (var j=0; j < temp.length; j++) {
		if ((temp[j] == "Jr.") || (temp[j] == "Sr.")) {
			html += temp[j];
		}
		else {
			html += temp[j].substring(0,1) + ". ";
		}
	}
	return html + " ";
}

/*Page.printEditorsAPAOrMLA = function(_citation, format)
{
	var html = '';
	
	if (format == 'apa')
	{
		html += ' In ';
	}
	else
	{
		html += ' Ed. '
	}
	var explode;
	//if (_citation.citation_id == '52') { alert(_citation.editor);}
	if (_citation.editor != "" && _citation.editor != undefined) 
	{
		explode = _citation.editor.split(';'); 
	}
	else return "";
	
	var arr = new Array();
	
	var one_editor;
	for(var i = 0; i < explode.length; i++)
	{
	//	
		one_editor = explode[i].split(',',2);
		
		if (format == 'apa')
		{
			if (one_editor.length > 1)
			{	
				one_editor[1] = one_editor[1].substring(1,2) + ". "
			}
		}
		arr.push(one_editor[1] + one_editor[0]);
	}
	
	if(arr.length > 1)
	{
		for(var e = 0; e < arr.length - 1; e++)
		{
			html += arr[e] + ", ";	
		}
		html = html.substring(0, html.length-2);
		html += " &amp; " + arr[arr.length-1];
	}
	else
	{
		html += arr[0];// + " (Ed.) ";
	}
	
	if (format == 'apa')
	{
		if (arr.length >1)
		{
			html += ' (Eds.) ';
		}
		else
		{
			html += ' (Ed.) ';
		}
	}
	
	
	return html;
}*/

Page.printEditorsAPAOrMLA = function(_citation, format)
{
	var html = '';
	if (format == 'apa')
	{
		html += Page.printEditors_APA(_citation);
	}
	else
	{
		html += Page.printEditors_MLA(_citation);
	}
	
	return html;

}

Page.printEditors_APA = function(_citation)
{
	var html = '';
	
	html += ' In ';

	var explode;
	if (_citation.editor != "" && _citation.editor != undefined) 
	{
		explode = _citation.editor.split(';'); 
	}
	else return "";
	
	var arr = new Array();
	
	var one_editor;
		
	for (var i = 0; i < explode.length; i++)
	{
		one_editor = explode[i].split(',',2);
		
		if (one_editor.length > 1)
		{	
			one_editor[1] = one_editor[1].substring(1,2) + "."
		}
		
//		if (_citation.citation_id == '52') { alert(one_editor[1]); alert(one_editor[0]);}

		arr.push(one_editor[1] + ' ' + one_editor[0]);
	}

  if (arr.length > 3) {
    html += arr[0] + ", et al.";
    return html;
  }

	if (arr.length > 1)
	{
		for (var e = 0; e < arr.length - 1; e++)
		{
			html += arr[e] + ", ";	
		}
		html = html.substring(0, html.length-2);
		html += " &amp; " + arr[arr.length-1];
	}
	else
	{
		html += arr[0];
	}
	
	if (arr.length > 1)
	{
		html += ' (Eds.) ';
	}
	else
	{
		html += ' (Ed.) ';
	}
	
	return html;	
}

Page.printEditors_MLA = function(_citation)
{
	var html = '';
	
	html += ' Ed. ';
	
	var explode;

	if (_citation.editor != "" && _citation.editor != undefined) 
	{
		explode = _citation.editor.split(';'); 
	}
	else return "";
	
	var arr = new Array();
	
	var one_editor;

	for (var i = 0; i < explode.length; i++)
	{
		one_editor = explode[i].split(',',2);
		
		arr.push(one_editor[1] + ' ' + one_editor[0]);
	}

	if (arr.length > 1)
	{
		for (var e = 0; e < arr.length - 1; e++)
		{
			html += arr[e] + ", ";	
		}
		html = html.substring(0, html.length-2);
		html += " and " + arr[arr.length-1]  + '.';
	}
	else
	{
		html += arr[0]  + '.';
	}
	

	return html;	
}

Page.printVolume = function(_citation) {
  var html = "";
  if (_citation.volume != "") {
    html += "Vol. " + _citation.volume;
  }
  return html;
}

Page.printVolumeAPA = function(_citation) {
  var html = "";
  if (_citation.volume != "") {
    html += "(Vol. " + _citation.volume + ") ";
  }
  return html;
}

Page.printEdition = function(_citation){

  var html = "";
 // English is a stupid language so now we need this thing.
  if (_citation.edition != "") {
   // Last number of the edition determines the ordinal.
   last_digit = _citation.edition.substring(_citation.edition.length-1, _citation.edition.length);
   switch (last_digit) {
     case '1':
      ordinal = "st";
      break;
     case '2': 
       ordinal = "nd";
       break;
     case '3':
       ordinal = "rd";
       break;
     default:
       ordinal = "th";
    }
     html+= " (" + _citation.edition + ordinal + " ed.).";
    }
  
  return html;
}

Page.printAPAStyleCitation = function(_citation)
{
	var html = '';
	var pubtype = '';
	var title = "";

	if(Page.pubtypes_json[_citation.pubtype] == undefined) { 
		pubtype = "misc";  // Default value if no pubtype available in pubtype_def
	}
	else { pubtype = _citation.pubtype; }
	
	// Pinkified empty or undefined apa_required_fields
	var citation_req; // Required fields array
	
	// Build required element list based on pubtype.
	citation_req = _citation;
	
	// Check for empty title and use temp variable.
	if((citation_req.title == "") || (citation_req.title == undefined)) {
		title = "";	
	}
	else title = citation_req.title;

	// Check for ? or ! in title.
	var temp = title.substring(title.length-1,title.length);
	if( temp == "?" || temp == "!") {
		title += " ";
	}
	else if ((temp == ".") || (temp == ""))
	{
	}
	else {
		title += ". ";
	}
	
	if (pubtype == "article") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
		html += "<em>" + citation_req.journal + "</em>";	
		if ((citation_req.number == "") && (citation_req.volume == "") && (citation_req.pages == ""))  // publication pending
		{
			html += ".";
		}
		else
		{
			html += ", ";
			if((citation_req.number == "") || (citation_req.number == undefined)) {
				html +=  "<em>" + citation_req.volume + "</em>, ";	
			}
			else { 
				html += "<em>" + citation_req.volume + "</em>";
				html +=  "(" + citation_req.number + "), "; 
			}
			html +=  citation_req.pages + ".";
		}
	}
	else if (pubtype == "book") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html +=  "<em>" + title + "</em>";
    html += Page.printVolume(_citation);
    html += Page.printEdition(_citation);
		html +=  citation_req.location + ": ";
		html +=  citation_req.publisher + ".";		
	}
	else if (pubtype == "inbook") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
    html += Page.printEdition(_citation);
		
		html += rtrim(Page.printEditorsAPAOrMLA(citation_req, 'apa')) + ", ";
    		//html +=  "In " + citation_req.editor + "(Eds.), "
		html +=  "<em>" +  citation_req.booktitle + "</em> ";
    html += Page.printVolumeAPA(_citation);
		html +=  "(pp. " + citation_req.pages + "). ";
	//	html +=  citation_req.pages + ". ";
		html +=  citation_req.location + ": ";
		html +=  citation_req.publisher + ".";	
	}
	else if (pubtype == "manual") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html +=  title;
		html +=  citation_req.edition + ". ";
		html +=  citation_req.organization + ". ";
	}
	else if (pubtype == "mastersthesis" || pubtype == "phdthesis" ) {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
		html += citation_req.school + ". ";
	}
	else if (pubtype == "proceedings" || pubtype == "inproceedings" || pubtype == "conference" || pubtype == "incollection"){
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
		html += Page.printEditorsAPAOrMLA(citation_req, 'apa');
		//html += "In " + citation_req.editor + "(Eds.), ";
		if((citation_req.organization == "") || (citation_req.organization == undefined)) {}
		else { html += citation_req.organization + ". "; }
		html += citation_req.pages + ". ";
		html += citation_req.location + ": ";
		html += citation_req.publisher + ".";		
	}
	else if (pubtype == "techreport") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
		html += citation_req.type + ". ";
		html += citation_req.number + ". ";
		html += citation_req.institution + ". ";
	}
	else if (pubtype == "unpublished") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
		html += citation_req.note + ". ";
	}
	else if (pubtype == "edited_book") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html +=  "<em>" + title + "</em>";
    html += Page.printEdition(_citation);
		html +=  citation_req.location + ": ";
		html +=  citation_req.publisher + ".";		
	}
	else if (pubtype == "translated_book") {
		html += Page.printAPAStyleAuthors(_citation);
		html += "(" + citation_req.year + "). ";
		html +=  "<em>" + title + "</em>";
    html += Page.printEdition(_citation);
		html +=  " (" +citation_req.translator + " Trans.) ";
    html += Page.printVolume(_citation);
		html +=  citation_req.location + ": ";
		html +=  citation_req.publisher + ".";		
	}
	else if (pubtype == "web_published") {
		html += Page.printAPAStyleAuthors(_citation);
		html += "(" + citation_req.year + "). ";
		html +=  "<em>" + title + "</em>.";	
		html += 'Retrieved from: ' + '<a href="' + _citation.url + '">' + _citation.url + '</a> on ' + _citation.date_retrieved + '.';
	}
	else {  // misc
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + citation_req.year + "). ";
		html += title;
		html += citation_req.howpublished + ".";	
	}
	
	html += Page.printURLsDOIsNotes(_citation);

	
//	else if (pubtype == "booklet") {
//		html += Page.printAPAStyleAuthors(_citation);
//		html += " (" + _citation.year + "). ";
//		html +=  title;
//		if(_citation.howpublished != "")  html +=  _citation.howpublished + ". ";
//	}
	//if(emptyFields)	return html+'</span>';
	//else 
	return html;
}

// ********* MLA ************
Page.printMLAStyleAuthors = function(_citation) {
	
	var auth_count = 1;
	var html = "";
	
	if ((_citation.author0ln != "") && (_citation.author0ln != "undefined")){
		if((_citation.author0fn != "") && (_citation.author0fn != "undefined")){
			html += _citation.author0ln + ", " + _citation.author0fn + "";
		}
		else
		{
			html += _citation.author0ln;//'[AUTHOR 1 NAME]';	
		}
	}
	else
	{
		html += _citation.author0ln;//'[AUTHOR 1 NAME]';
	}
	
	for (var j = 1; j < 6; j++) {
		var ln = "author" + j + "ln"; // tempVar
		var fn = "author" + j + "fn";

		if ((_citation[ln] != "") && (_citation[ln] != "undefined"))
		{
			auth_count++;
			var next_ln = "author" + (j+1) + "ln"; // tempVar
			var next_fn = "author" + (j+1) + "fn";
			if((_citation[next_ln] != "") && (_citation[next_ln] != "undefined"))
			{
				html += ", "+ _citation[fn] + " " +  _citation[ln] + "";
			}
			else
			{
				html += " and "+_citation[fn] + " " +  _citation[ln] + ".";	
			}
		}
	}
	
	if(_citation.pubtype == "edited_book")
	{
		if(auth_count > 1)
		{
			return " Eds. " + html + " ";
		}
		else
		{
			return " Ed. " + html + " ";	
		}
	}
	else
	{
		return html + " ";	
	}
}

Page.flagEmptyFields = function(_citation, pubtype, format)
{
	// Pinkified empty or undefined apa_required_fields
	var citation_req = new Array();  // Required fields array
	citation_req['found'] = false;
	
	var labels = new Array();
	var ids = new Array();
	var html_start = '<span class="missingFlag">';
	var html_end = '</span>';
	var required_fields = (format == 'APA') ? 'apa_required_fields' : 'mla_required_fields';
		
	// Build required element list based on pubtype.
	for(var i in Page.pubtypes_json[pubtype][required_fields])
	{
		var id = Page.pubtypes_json[pubtype][required_fields][i];
		
		if(Page.fields_arr[id] != undefined) 
		{
			if(_citation[id] == "")	{
				citation_req[id] = html_start+'['+Page.fields_arr[id][0].toUpperCase()+']'+html_end; // Uppercase all letters.
				citation_req['found'] = true;
			}
			else {
				citation_req[id] = _citation[id]; 	
			}
		}
		else if(id == "title" || id == "year") 
		{
			if(_citation[id] == "")	{
				citation_req[id] = html_start+'['+id.toUpperCase()+']'+html_end; // Uppercase all letters.
				citation_req['found'] = true;
			}
			else {
				citation_req[id] = _citation[id]; 	
			}
		}
		else {} // need to check for first author as well.
	}
	
	return citation_req;
}

Page.printEditorsMLA = function(_citation)
{
	var html = "";
	var explode;
	
	var myRegExp = /[EDITOR]/;
	var string1 = "" + _citation.editor;
	var matchPos1 = string1.search(myRegExp);
	
	if(matchPos1)
	{
		html += " Ed. " + _citation.editor + ".";
		return html;
	}
	
	if(_citation.editor != undefined)
	{
		explode = _citation.editor.split(','); 
	}
	else return "";
	
	var arr = new Array();
	
	// explode.length - 2 is to exclude the last two cells in the array. 2 cells = 1 name. 
	for(var i = 0; i < explode.length - 2; i = i + 2)
	{
		if(_citation.editor != "" && _citation.editor != undefined) 
		{
			arr.push(explode[i+1] + " " + explode[i]);
		}
	}
	
	if(arr.length > 1)
	{
		html += " Eds. ";
		for(var e = 0; e < arr.length - 1; e++)
		{
			html += arr[e] + ", ";	
		}
		html = html.substring(0, html.length-2);
		html += " and " + arr[arr.length-1] + ".";
	}
	else
	{
		html += " Ed. " + arr[0] + ".";
	}
	
	return html;
}

Page.printMLAStyleCitation = function(_citation)
{
	var html = "";
	var pubtype = "";
	
	if(Page.pubtypes_json[_citation.pubtype] == undefined) { 
		pubtype = "misc";  // Default value if no pubtype available in pubtype_def
	}
	else { pubtype = _citation.pubtype; }
	
	// Pinkified empty or undefined apa_required_fields
	var citation_req; // Required fields array
	
	// Build required element list based on pubtype.
	citation_req = _citation;
	
	// Check for empty title and use temp variable.
	if((citation_req.title == "") || (citation_req.title == undefined)) {
		title = "";	
	}
	else title = citation_req.title;
	
	// Check for ? or ! in title.
	var temp = title.substring(title.length-1,title.length);
	if( temp == "?" || temp == "!") {
		title += " ";
	}
	else {
		title += ". ";
	}
	
	if (pubtype == "article") {
		html += Page.printMLAStyleAuthors(_citation);
		html += " &#34;"+ title + "&#34; ";
		html += " <u>" + citation_req.journal + "</u> ";
		html += "" + citation_req.volume + "";
		html += "." + citation_req.number + " ";
		html += "(" + citation_req.year + "): ";
		html +=  citation_req.pages + "."
	}
	else if (pubtype == "book") {
		html += Page.printMLAStyleAuthors(_citation);
		html += " <u>" + title + "</u>";
		html += " " + citation_req.location + ": ";
		html += "" + citation_req.publisher + ","	
		html += " " + citation_req.year + ".";	
	}
	else if ((pubtype == "inbook") || (pubtype == "unknown")) {
		html += Page.printMLAStyleAuthors(_citation);
		html += " &#34;"+ title + "&#34;";
		html += " <u>" +  citation_req.booktitle + "</u>.";
		html += Page.printEditorsAPAOrMLA(citation_req, 'mla');
		//html += " Ed. " + citation_req.editor + ".";
		html += " " + citation_req.location + ": ";
		html += " " + citation_req.publisher + ",";
		html += " " + citation_req.year + ".";	
		html += " " + citation_req.pages + ".";			
	}
	else if (pubtype == "manual") {
		html += Page.printMLAStyleAuthors(_citation);
		html +=  title;
		html += " (" + citation_req.year + "). ";
		if(citation_req.edition != "")  html +=  citation_req.edition + ". ";
		if(citation_req.organization != "")  html +=  citation_req.organization + ". ";
	}
	else if (pubtype == "mastersthesis" || pubtype == "phdthesis" ) {
		html += Page.printMLAStyleAuthors(_citation);
		html +=  title;
		html += " " + citation_req.year + ". ";
		if(citation_req.school != "")  html +=  citation_req.school + ". ";
	}
	else if (pubtype == "proceedings" || pubtype == "inproceedings" || pubtype == "conference" || pubtype == "incollection"){
		html += Page.printMLAStyleAuthors(_citation);
		html += " &#34;"+ title + "&#34;";
		if(citation_req.editor != "")	html +=  " " + citation_req.editor + "(Eds.), ";
		if(citation_req.organization != "")  html +=  citation_req.organization + ". ";
		if(citation_req.publisher != "")  html +=  citation_req.publisher + ".";
		if(citation_req.location != "")  html +=  citation_req.location + ", ";
		html += " " + citation_req.year + ". ";
		if(citation_req.pages != "")  html +=  citation_req.pages + ". ";
	}
	else if (pubtype == "techreport") {
		html += Page.printMLAStyleAuthors(_citation);
		html += " &#34;" + title + "&#34;";
		if(citation_req.type != "")  html +=  citation_req.type + ". ";
		if(citation_req.number != "")  html +=  citation_req.number + ". ";
		html += " " + citation_req.year + ". ";
		if(citation_req.institution != "")  html +=  citation_req.institution + ". ";
	}
	else if (pubtype == "unpublished") {
		html += Page.printMLAStyleAuthors(_citation);
		html += " &#34;"+ title + ".&#34;";
		html += " (" + citation_req.year + "). ";
		if(citation_req.note != "")  html +=  citation_req.note + ". ";
	}
	else if (pubtype == "edited_book") {
		html += "<u>" + title + "</u>";
		html += Page.printMLAStyleAuthors(_citation);
		if(citation_req.location != "")  html +=  citation_req.location + ": ";
		if(citation_req.publisher != "") html +=  citation_req.publisher + ", ";
		html += " " + citation_req.year + ". ";
	}
	else if (pubtype == "translated_book") {
		html += Page.printMLAStyleAuthors(_citation);
		html += " <u>" + title + "</u>";
		if(citation_req.translator != "")  html +=  " (" +citation_req.translator + " Trans.) ";
		if(citation_req.location != "")  html +=  citation_req.location + ": ";
		if(citation_req.publisher != "") html +=  citation_req.publisher + ".";
		html += " " + citation_req.year + ". ";
	}
	else if (pubtype == "web_published") {
		html += Page.printMLAStyleAuthors(_citation);
		html += " <u>" + title + "</u>";
		html += " " + citation_req.year + ". ";
		html += 'Retrieved from: ' + '<a href="' + _citation.url + '">' + _citation.url + '</a> on ' + _citation.date_retrieved + '.';
	}
	else {  // misc
		html += Page.printMLAStyleAuthors(_citation);
		html += " " + title;
		html +=  citation_req.howpublished + ".";	
		html += " " + citation_req.year + ". ";
	}
	
	html += Page.printURLsDOIsNotes(_citation);
	return html;
}

Page.printURLsDOIsNotes = function(_citation)
{
	var html = '';
	if (Page.show_URLs_flag == 1)
	{
		if ((_citation.url != "") && (_citation.pubtype != 'web_published'))
		{
			html += ' Retrieved from: <a href="' + _citation.url + '">' + _citation.url + '</a>';
			if ((_citation.date_retrieved != '') && (_citation.date_retrieved != '0'))
			{
				html += ' on ' + _citation.date_retrieved;
			}
			html += '. '
		}
		if(_citation.doi != "") 
		{
			html += ' doi: ' + _citation.doi;
		}
	}
	if (Page.show_notes_flag == 1)
	{
		if(_citation.note != "") html += ' [' + _citation.note + ']';
	}
	
	if (Page.show_abstracts_flag == 1)
	{
		if(_citation.abstract != "") html += '<br><br>Abstract: ' + _citation.abstract;
	}
	
	return html;
}

// ********* HTML ***********
Page.printHTMLListStyleCitation = function(_citation)
{
	var html = "";
	html += "&lt;li&gt;";
	var pubtype = _citation.pubtype;
	var title;
	if(_citation.title.substring(_citation.title.length-1,_citation.title.length) == "?") {
		title = _citation.title + " ";	
	}
	else {
		title = _citation.title + ". ";
	}
	// Check for ? or ! in title.
	
	
	if (pubtype == "article") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		html += "&lt;em&gt;" + _citation.journal + "&lt;/em&gt;, ";
		html +=  "&lt;em&gt;" +_citation.volume + "&lt;/em&gt;";
		if (_citation.number != "")
			html += "(" + _citation.number + "), ";
		else 
			html += ", ";
		html +=  _citation.pages + "."
	}
	else if (pubtype == "book") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  "&lt;em&gt;" +  title + "&lt;/em&gt; "
		html +=  _citation.location + ": ";
		html +=  _citation.publisher + "."		
	}
	else if (pubtype == "inbook") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html += title;
    html += rtrim(Page.printEditorsAPAOrMLA(_citation, 'apa')) + ", ";
    		//html +=  "In " + citation_req.editor + "(Eds.), "
		html +=  "&lt;em&gt;" +  _citation.booktitle + "&lt;/em&gt; "
    html += Page.printEdition(_citation);
    html += Page.printVolumeAPA(_citation);
		html +=  "(pp. " + _citation.pages + "). ";
		html +=  _citation.location + ": ";
		html +=  _citation.publisher + ".";	
	/*	html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		html +=  "In " + _citation.editor + "(Eds.), "
		html +=  "&lt;em&gt;" +  _citation.booktitle + "&lt;/em&gt;. "
		html +=  _citation.pages + ". "
		html +=  _citation.location + ": ";
		html +=  _citation.publisher + "."	*/			
	}
	else if (pubtype == "manual") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		if(_citation.edition != "")  html +=  _citation.edition + ". ";
		if(_citation.organization != "")  html +=  _citation.organization + ". ";
	}
	else if (pubtype == "mastersthesis" || pubtype == "phdthesis" ) {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		if(_citation.school != "")  html +=  _citation.school + ". ";
	}
	else if (pubtype == "proceedings" || pubtype == "inproceedings" || pubtype == "conference" || pubtype == "incollection"){
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		if(_citation.editor != "")	html +=  "In " + _citation.editor + "(Eds.), ";
		if(_citation.organization != "")  html +=  _citation.organization + ". ";
		if(_citation.pages != "")  html +=  _citation.pages + ". ";
		if(_citation.location != "")  html +=  _citation.location + ": ";
		if(_citation.publisher != "")  html +=  _citation.publisher + ".";		
	}
	else if (pubtype == "techreport") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		if(_citation.type != "")  html +=  _citation.type + ". ";
		if(_citation.number != "")  html +=  _citation.number + ". ";
		if(_citation.institution != "")  html +=  _citation.institution + ". ";
	}
	else if (pubtype == "unpublished") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		if(_citation.note != "")  html +=  _citation.note + ". ";
	}
	else if (pubtype == "edited_book") {
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  "&lt;em&gt;" + title + "&lt;/em&gt;";
		if(_citation.location != "")  html +=  _citation.location + ": ";
		if(_citation.publisher != "") html +=  _citation.publisher + ".";		
	}
	else if (pubtype == "translated_book") {
		html += Page.printAPAStyleAuthors(_citation);
		html += "(" + _citation.year + "). ";
		html +=  "&lt;em&gt;" + title + "&lt;/em&gt; ";
		if(_citation.translator != "")  html +=  " (" +_citation.translator + " Trans.) ";
		if(_citation.location != "")  html +=  _citation.location + ": ";
		if(_citation.publisher != "") html +=  _citation.publisher + ".";		
	}
	else if (pubtype == "web_published") {
		html += Page.printAPAStyleAuthors(_citation);
		html += "(" + _citation.year + "). ";
		html +=  "&lt;em&gt;" + title + "&lt;/em&gt; ";
		html += 'Retrieved from: ' + '<a href="' + _citation.url + '">' + _citation.url + '</a> on ' + _citation.date_retrieved + '.';
	}
	else {  // misc
		html += Page.printAPAStyleAuthors(_citation);
		html += " (" + _citation.year + "). ";
		html +=  title;
		html +=  _citation.howpublished + ".";	
	}
	html += "&lt;/li&gt;";
	return html;
}


