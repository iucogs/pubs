Page.printAuthorRowText = function(_citation)
{
	var html = "";
	var lnValue;
	var fnValue; 

	for (var i = 0; i < 6; i++)
	{
		lnValue = _citation['author'+i+'ln'];
		fnValue = _citation['author'+i+'fn']; 
			
		for (var j=0; j<Page.authorNameSubstitutions.length; j++)
		{
			if ((Page.authorNameSubstitutions[j].orig_ln == lnValue) && (Page.authorNameSubstitutions[j].orig_fn == fnValue))
			{
				lnValue = Page.authorNameSubstitutions[j].new_ln;
				fnValue = Page.authorNameSubstitutions[j].new_fn;	
			}
		}
		
		var style = 'style="padding-left:30px"';
		html += '<tr height="26px" align="left" style="vertical-align:middle">';
		html += '<td width="8%" align="center">' + (i+1) + '</td>';
		html += '<td ' + style + ' width="44%">' + lnValue + '</td>';
		html += '<td ' + style + ' width="34%">' + fnValue + '</td>';
		html += '<td width="14%"></td>';
		html += '</tr>';
	}
	return html;
}

Page.printTableRowText = function(label, _field)
{
	return '<tr height="26px" align="left" style="vertical-align:middle"><td><b>'+label+': </b></td><td style="width:100%">' + _field + '</td></tr>';
}

Page.printTableRowScrollableDiv = function(label, _field, height)
{
	var html = '';  //id="'+name+'"
	html += '<tr align="left" valign="top"><td><b>'+label+': </b></td><td style="width:100%">';
	html += '<div style="position:relative;height:'+height+'px;overflow:auto;">';  //padding:0.5em; margin: 0.5em;width:20em;
	html += _field; 
	html += '</div>';
	html += '</td></tr>';
	return html;
}

Page.printTableRowDisabledTextarea = function(label, _field, rows, cols)
{
	return '<tr align="left" valign="top"><td><b>'+label+': </b></td><td style="width:100%"><textarea rows="'+rows+'" cols="'+cols+'" disabled="disabled">' + _field + '</textarea></td></tr>';
}

Page.enterAuthorInfo = function(_citation, fieldFlag, citation_suffix) 
{
	var html = '';
			
	html += Page.printTableTop('author', citation_suffix);
	
	if (_citation.pubtype == 'edited_book')
	{
		html += '<th>Editor</th><th>Last Name</th><th>First Name</th><th></th>';
	}
	else
	{
		html += '<th>Author</th><th>Last Name</th><th>First Name</th><th></th>';
	}
	
	if(fieldFlag == "text")
	{
		html += Page.printAuthorRowText(_citation);	
	}
	else
	{
		html += Page.printAuthorRow(_citation, citation_suffix);
	}
	html += '</table>';
	html += Page.tableSpacing(citation_suffix);	
	return html; 
}


Page.enterConstantByPubtypeInfo = function(_citation, fieldFlag, citation_suffix)
{
	var html = '';
	html += Page.printTableTop('constantByPubtype', citation_suffix);
	
	if(fieldFlag == "text")  // only for printing saved info after save
	{
		html += Page.printTableRowText('Title', _citation.title);
		html += Page.printTableRowText('Year', _citation.year);
	}
	else // hidden or visible textfield
	{
		html += Page.printTableRow('Title', 'title', _citation.title, '33', citation_suffix, 1, _citation.pubtype);
		html += Page.printTableRow('Year', 'year', _citation.year, '10', citation_suffix, 0, _citation.pubtype);
	}
	html += '</table>';
	return html;
}

Page.enterChangingByPubtypeInfo = function(_citation, pubtype, flagField, citation_suffix)
{
	var html = '';
	
	citation_suffix = (citation_suffix == undefined) ? "" : citation_suffix;

	html += Page.printTableTop('changingByPubtype', citation_suffix);

	if(Page.pubtypes_json[pubtype] == undefined || Page.pubtypes_json[pubtype] == "")
	{
		pubtype = "misc";  // Default value if no pubtype available in pubtype_def
	}
	
	if(flagField == "text")
	{
		for(var i in Page.pubtypes_json[pubtype].fields)
		{
			var field_name = Page.pubtypes_json[pubtype].fields[i];

			if(_citation[Page.pubtypes_json[pubtype].fields[i]] == undefined) {					// If the citation field is undefined, treat it as empty
				//html += Page.printTableRowText(Page.pubtypes_json[pubtype].fields[i],"");
			}
			else {
				
				var value_holder = _citation[field_name];
				if(value_holder == undefined) value_holder = ""; else value_holder = _citation[field_name];
				if(Page.fields_arr[field_name] == undefined) {  // Check for fields that doesn't exist and return nothing.
					// skip
				}
				else html += Page.printTableRowText(Page.fields_arr[field_name][0],value_holder);
			}
		}
	}
	else
	{
		for(var i in Page.pubtypes_json[pubtype].fields)
		{		
			var field_name = Page.pubtypes_json[pubtype].fields[i];
			if(Page.fields_arr[field_name] != undefined) {  // Check for fields that doesn't exist and return nothing.
				var size = Page.fields_arr[field_name][1];
				var label = Page.fields_arr[field_name][0];
				
			//	var cit_temp = Page.citations_array_b[temp];
				if(_citation[field_name] == undefined) {					// If the citation field is undefined, treat it as empty
					html += Page.printTableRow(label, field_name, "", size, citation_suffix, 0, pubtype);
				}
				else if ((field_name == "publisher") || (field_name == "journal")) {
					html += Page.printTableRow(label, field_name, _citation[field_name], size, citation_suffix, 1, pubtype);
				}
				else if(field_name == "editor") {
					html += Page.printTableRow(label, field_name, _citation[field_name], size, citation_suffix, 1, pubtype);
					html += '<tr><td></td><td><font size="1" color="dark grey"><em><strong>';
					html += 'Editors Format: Lastname1, Firstname1(s); Lastname2, Firstname2(s);...';
					html += '</strong></em></font></td></tr>'; 
				}
				else {
					html += Page.printTableRow(label, field_name,_citation[field_name], size, citation_suffix, 0, pubtype);	
				}
			}	
		}
	}
	
	html += "</table>";
	return html;
}

Page.changePubtypeInfoInTab = function(pubtype, citation_suffix)
{
	var citation_suffix = Page.get_citation_suffix_of_active_tab(tabView_b);
	var active_index = tabView_b.get('activeIndex');
	Page.create_working_citation(citation_suffix);
	for (var i in Page.working_citation)
	{
		if (Page.working_citation[i] != "")
		{
			Page.citations_array_b[active_index-1][i] = Page.working_citation[i];
		}
	}
	return Page.enterChangingByPubtypeInfo(Page.citations_array_b[active_index-1], pubtype, "", citation_suffix); 
}

Page.refreshRaw = function() // needed for context menu to function in safari
{
}


Page.enterRawInfo = function(_citation, fieldFlag, citation_suffix) 
{
	var html = '';
	Page.rawTemporary = _citation.raw;
	html += Page.printTableTop('raw', citation_suffix);
	
	html += '<input size="10" type="hidden" id="citation_id'+citation_suffix+'" name="citation_id'+citation_suffix+'" value="'+_citation.citation_id+'">';


	if(fieldFlag == "text")
	{
		html += Page.printTableRowText('Raw', _citation.raw);
	}
	/*else if(fieldFlag == "new")
	{
		
	}*/
	else if((citation_suffix.indexOf("_a") >= 0) || (citation_suffix.indexOf("_b") >= 0))
	{
		html += '<tr align="left" valign="top"><td><b>Raw:</b></td><td>';
		html += '<div style="z-index:' + Page.zCounter-- + '">';
		html += '<textarea id="raw'+citation_suffix+'" name="raw'+citation_suffix+'" rows="2" cols="65" readonly="readonly">' + _citation.raw + '</textarea>';
		html += '</div>';
		html += '</td></tr>'; 
	}
	else
	{
		html += '<tr align="left" valign="top"><td>';
		
		html += '<b>Raw:</b></td><td>';
		html += '<div id="the_raw_div" style="z-index:' + Page.zCounter-- + '">';
		html += '<textarea id="raw" name="raw" rows="3" cols="52" onchange="this.value=Page.rawTemporary;" onkeyup="this.value=Page.rawTemporary;">' + _citation.raw + '</textarea>'; //readonly="readonly"
			//	html += '<textarea id="raw" name="raw" rows="3" cols="52" onselect="Page.safariSelection=this.value.substring(this.selectionStart,this.selectionEnd);alert(this.selectionStart);">' + _citation.raw + '</textarea>'; //readonly="readonly"

		html += '</div>';
		html += '</td></tr>';
		
		
		html += '<tr align="left" valign="top"><td>';

		html += '<b>Preview:</b></td><td> <div id="the_preview_div">';
		html += '</div>';
		html += '</td></tr>';
		
		html += '<tr align="center" valign="top"><td colspan="2"><input type="button" value="Update Preview" onclick="Page.updatePreview();"></td></tr>';
	}	
	html += '</table>';
	return html;
}

Page.updatePreview = function(theMenu)
{
	Page.create_working_citation('');
	document.getElementById('the_preview_div').innerHTML = Page.printAPAStyleCitation(Page.working_citation);
}

Page.enterAbstractKeywords = function(value, fieldFlag, citation_suffix, field_name, label)
{	
	var html = '';
	html += Page.printTableTop(field_name, citation_suffix);
	if (fieldFlag == "text")
	{
		html += Page.printTableRowScrollableDiv(label, value,'80');
	}
	else if (citation_suffix.indexOf("_a") >= 0)  
	{	
		html += '<input type="hidden" id="' + field_name + citation_suffix +'" name="' + field_name + citation_suffix +'" value="' + value + '">';
		html += Page.printTableRowScrollableDiv(label, value,'120');
	}
	else //if (citation_suffix.indexOf("_b") >= 0)  or regular save
	{
		html += '<tr align="left" valign="top"><td><b>' + label + ':</b></td><td>';
		html += '<div style="z-index:' + Page.zCounter-- + '">';
		html += '<textarea id="' + field_name + citation_suffix + '" name="' + field_name + citation_suffix + '" rows="3" cols="20" style="width:340px;">' + value + '</textarea>';  // 52
		html += '</div>';
		html += '</td></tr>';
	}
	html += '</table>';
	return html; 
}

Page.enterAdditionalInfo = function(_citation, fieldFlag, citation_suffix)
{
	var html = '';
	//html += '<table style="border: 2px solid #7D110C;"  width="100%">';
	html += Page.printTableTop('url_doi', citation_suffix);
	if(fieldFlag == "text")
	{
		html += Page.printTableRowText('URL', _citation.url);
		html += Page.printTableRowText('DOI', _citation.doi);
	}
	else
	{
		html += Page.printTableRow('URL', 'url', _citation.url, '50', citation_suffix, 0, _citation.pubtype);
		html += Page.printTableRow('DOI', 'doi', _citation.doi, '50', citation_suffix, 0, _citation.pubtype);
	}

	html += '<input type="hidden" id="citation_id" name="citation_id" value="' + _citation.citation_id + '">';  // Citation is now hidden
	html += "</table>";
	
	html += Page.enterFilename(_citation, fieldFlag, citation_suffix);
	
	html += Page.printTableTop('note', citation_suffix);
	//html += '<table style="border: 2px solid #7D110C;"  width="100%">';
	if(fieldFlag == "text")
	{
		html += Page.printTableRowText('Note', _citation.note);
	}
	else
	{
		html += Page.printTableRow('Note', 'note', _citation.note, '50', citation_suffix, 0, _citation.pubtype); //60
	}
	html += '</table>';

	return html;
}

Page.enterFilename = function(_citation, fieldFlag, citation_suffix)
{
	var html = '';
	html += Page.printTableTop('file', citation_suffix);
	if(fieldFlag == "text")
	{
		html += Page.printTableRowText('File', _citation.filename);
	}
	else if (citation_suffix.indexOf("_a") >= 0)  
	{
		html += '<input type="hidden" id="filename'+ citation_suffix +'" name="filename'+ citation_suffix +'" value="' + _citation.filename + '">';
		html += Page.printTableRowText('File', _citation.filename);
	}
	else // b or regular
	{
		html += '<input type="hidden" id="filename'+ citation_suffix +'" name="filename'+ citation_suffix +'" value="' + _citation.filename + '">';
		html += '<tr><td>' + Page.printUploadDialog(_citation, citation_suffix) + '</td></tr>';
	}
	html += '</table>';
	return html;
}

//Page.printAuthorRow	- Used by nextEntry()
//					- Generate <tr></tr> for author table
Page.printAuthorRow = function(_citation, citation_suffix)
{
	var html = "";
	var lnValue;
	var fnValue;
	for (var i = 0; i < 6; i++)
	{
		lnValue = _citation['author'+i+'ln'];
		fnValue = _citation['author'+i+'fn'];
		
		for (var j=0; j<Page.authorNameSubstitutions.length; j++)
		{
			if ((Page.authorNameSubstitutions[j].orig_ln == lnValue) && (Page.authorNameSubstitutions[j].orig_fn == fnValue))
			{
				lnValue = Page.authorNameSubstitutions[j].new_ln;
				fnValue = Page.authorNameSubstitutions[j].new_fn;	
			}
		}
		
		if ((i == 0) && (lnValue == ""))
		{
			lnValue = '';
		}
		
		if ((i == 0) && (fnValue == ""))
		{
			fnValue = '';
		}
		
		idValue = (_citation['author'+i+'id'] == undefined) ? "" : _citation['author'+i+'id'];

		var hidden_id_input = '<input type="hidden" name="author'+i+'id'+citation_suffix + '" id="author'+i+'id' +citation_suffix + '" value="'+idValue+'" />';

		//} // Check Page.returnChildValue to put a catch on case where parent is undefined.
		html += '<tr height="26px" align="left" style="vertical-align:middle">';
		
		html += '<td width="8%" align="center">';
		
		html += (i+1) + '</td>';
		html += "<td width='44%'>" + hidden_id_input + Page.printTextBoxAutocomplete('author'+i+'ln'+citation_suffix, lnValue, '16') + "</td>"
		//	+ "<td>" + Page.printTextBox('author'+i+'fn', fnValue, '20') + "</td>"
			+ "<td width='34%'>" + Page.printTextBoxAutocomplete('author'+i+'fn'+citation_suffix, fnValue, '12') + "</td>";
			
		html += "<td width='14%'>";
		if (citation_suffix.indexOf("_a") < 0)	
		{
			html += Page.printAuthorMoveButtons(i, citation_suffix);
		}
		html += "</td></tr>";
	}
	if (citation_suffix.indexOf("_b") >= 0) 
	{
		html += '<tr id="add_author_row'+citation_suffix+'"><td align="center">';
		html += '<span class="link pointerhand" onclick="Page.showAuthorRows(\'' + citation_suffix + '\');">Add</span>';
		html += '</td><td></td><td></td><td></td></tr>';
	}

	return html;
}


Page.showAuthorRows = function(full_citation_suffix)
{
	var citation_suffix_a = Page.get_citation_suffix_of_active_tab(tabView_a);
	for (var i = 1; i <= 6; i++)
	{
		document.getElementById('author_table' + full_citation_suffix).rows[i].style.display = ''; //Show
		document.getElementById('author_table' + citation_suffix_a).rows[i].style.display = ''; //Show

	}
	document.getElementById('add_author_row'+full_citation_suffix).style.display = 'none'; 		// Hide
//	var heightStr = document.getElementById('author_table' + full_citation_suffix).offsetHeight + 'px'; // get height of _b author table
//	document.getElementById('author_table' + citation_suffix_a).style.height = heightStr;
	//resizeContainerDivs(document.getElementById('author_fields' + full_citation_suffix), div_b, div_c)
	Page.alignActiveTabs();
}


Page.hideEmptyAuthorRowsAtEndOfTable = function()
{
	var citation_suffix_a = Page.get_citation_suffix_of_active_tab(tabView_a);
	var citation_suffix_b = Page.get_citation_suffix_of_active_tab(tabView_b);
	if (citation_suffix_b =='>')
	{	
		return;
	}
		
	var lastNonEmptyAuthorRow = 5;
	for (var i=5; i>0; i--)
	{
		if ((document.getElementById('author'+i+'ln'+citation_suffix_a).value == '') && (document.getElementById('author'+i+'ln'+citation_suffix_b).value == ''))
		{
			lastNonEmptyAuthorRow--;
			document.getElementById('author_table'+citation_suffix_a).rows[i+1].style.display = 'none'; 		// Hide
			document.getElementById('author_table'+citation_suffix_b).rows[i+1].style.display = 'none'; 		// Hide
			document.getElementById('author_table_buttons').rows[i+1].style.display = 'none'; 		// Hide	
		}
		else
		{
			break;
		}
	}

	if  (lastNonEmptyAuthorRow == 5)
	{
		document.getElementById('add_author_row'+citation_suffix_b).style.display = 'none'; //Hide
	}
}

Page.printAuthorMoveButtons = function(i, citation_suffix)
{
	return '<input type="image" src="' + Page.document_root + 'images/up.png" width="15" height="15" title="Up" onclick="Page.moveAuthorUp(' + i + ', \'' + citation_suffix + '\');" />'
			+ '&nbsp;'
			+ '<input type="image" src="' + Page.document_root + 'images/down.png" width="15" height="15" title="Down" onclick="Page.moveAuthorDown(' + i + ', \'' + citation_suffix + '\');" />'
			+ '&nbsp;'
			+ '<input type="image" src="' + Page.document_root + 'images/remove.png" width="15" height="15" title="Remove" onclick="Page.removeAuthor(' + i + ', \'' + citation_suffix + '\'); " />';
}

Page.printMoveRightButtons = function(pubtype, citation_suffix)
{
	var html = '';
	var citation_suffix_a = Page.get_citation_suffix_of_active_tab(tabView_a);
	
	var move_buttons_tt_array = new Array("author0ln_move_right_button", "author1ln_move_right_button", "author2ln_move_right_button", "author3ln_move_right_button", "author4ln_move_right_button", "author5ln_move_right_button", "title_move_right_button", "year_move_right_button");
	html += '<div id="padding_div"></div>';
	// Start
	var scrollable_div_height = Page.getViewportHeightForScrollableDivs();// + findPos(document.getElementById('top_div_a0'))[1];
	html += '<div id="scrollable_div_c" style="height:'+scrollable_div_height+'px;overflow:hidden">';
	
	html += '<div id="top_div_buttons" style="margin-bottom:5px;">';
	html += Page.printTableTop('raw', 'buttons');
	html += '<tr><td>&nbsp;';
	html += '</td></tr>';
	html += '</table>';
	html += Page.tableSpacing(citation_suffix);
	html += '</div>';  // Raw and Citation ID
	
	// Author Fields
	//html += '<div id="author_fields" style="border: 2px solid rgb(12, 17, 125);">'; 
	html += '<div id="author_fields_buttons" class="panel_div_buttons">'; 
	
	html += Page.printTableTop('author', 'buttons');
	html += '<tr><td>&nbsp;</td></tr>'; // header row
	for (var i=0; i<6; i++) {
			html += Page.printOneMoveRightButton('author' + i + 'ln');
	}
	html += '</table>';
	html += '</div>';
	
	// constantByPubtype_fields
	html += '<div id="constantByPubtype_fields_buttons" class="panel_div_buttons">';
	html += Page.printTableTop('constantByPubtype', 'buttons');
	html += Page.printOneMoveRightButton('title');
	html += Page.printOneMoveRightButton('year');
	html += '</table>';
	html += Page.tableSpacing(citation_suffix);
	html += '</div>';
	
	// pubtype_div
	html += '<div id="pubtype_div_buttons" class="panel_div_buttons">';
	html += Page.printTableTop('pubtype', 'buttons');
	html += '<tr><td>';
	html += '</td></tr>';
	html += '</table>';
	html += Page.tableSpacing(citation_suffix);
	html +='</div>';

	// changingByPubtype_fields
	html += '<div id="changingByPubtype_fields_buttons" class="panel_div_buttons">';

	html += '</div>';
	
	// abstract_keyword_div
	html += '<div id="abstract_div_buttons" class="panel_div_buttons">';
	html += Page.printButtonDivsTable('abstract', citation_suffix);
	html += '</div>';
	
	html += '<div id="keyword_div_buttons" class="panel_div_buttons">';
	html += Page.printButtonDivsTable('keywords', citation_suffix);
	html += '</div>';
	
	// additional_fields
	html += '<div id="additional_fields_buttons" class="panel_div_buttons">';
	
	html += Page.printTableTop('url_doi', 'buttons');
	html += Page.printOneMoveRightButton('url');
	html += Page.printOneMoveRightButton('doi');
	//move_buttons_tt_array.push('url_doi' + '_move_right_button');
	html += '</table>';
	html += Page.tableSpacing(citation_suffix);
//
//	html += Page.printTableTop('file', 'buttons');
//	html += '<tr><td>' + Page.printOneMoveRightButton('keywords') + '</td></tr>';
//	move_buttons_tt_array.push('file' + '_move_right_button');
//	html += '</table>';
//	html += Page.tableSpacing(citation_suffix);
	
	//html += Page.printButtonDivsTable('url_doi', citation_suffix);
	html += Page.printButtonDivsTable('file', citation_suffix);
	html += Page.printButtonDivsTable('note', citation_suffix);
	
	html += '</div>';
	
	// scrollable_div_c closing
	html += '</div>'; 
	
	// Write button_div html onto panel
	document.getElementById('buttons_div').innerHTML = html;
	
	/*for (var i=
	if  ((document.getElementById('author_row' + citation_suffix_a + '_'+i).style.display == '') || (document.getElementById('author_row' + citation_suffix + '_'+i).style.display == '')) //show
	{
	}*/
	
	Page.createMoveButtonToolTips(move_buttons_tt_array);
}



Page.printButtonDivsTable = function(field, citation_suffix)
{
	var html = '';
	html += Page.printTableTop(field, 'buttons');
	html += Page.printOneMoveRightButton(field);
	//move_buttons_tt_array.push(field + '_move_right_button');
	html += '</table>';
	html += Page.tableSpacing(citation_suffix);
	return html;
}

Page.printOneMoveRightButton = function(field)
{
	var html = '';
	html += '<tr height="26px" align="left" style="vertical-align:middle"><td>';
	html +=  '<input style="height:22px" type="button" name="' + field + '" id="' + field + '_move_right_button" value=">" onclick="Page.moveTextRight(\'' + field + '\');"/>';
	html += '</td></tr>';
	return(html);
} 

Page.moveTextRight = function(field)
{
	var from_field = field + Page.get_citation_suffix_of_active_tab(tabView_a);
	var to_field = field + Page.get_citation_suffix_of_active_tab(tabView_b);

	document.getElementById(to_field).value = document.getElementById(from_field).value;
	if (from_field.substr(0,6) == 'author')
	{
		from_field = from_field.replace('ln','fn');
		to_field = to_field.replace('ln', 'fn');
		document.getElementById(to_field).value = document.getElementById(from_field).value;
	}
}

// removeAuthor	- Used by printAuthorRow()
//				- Remove author entry in textbox
Page.removeAuthor = function(num, citation_suffix)
{
	document.getElementById('author'+ num + 'ln' + citation_suffix).value = "";
	document.getElementById('author'+ num + 'fn' + citation_suffix).value = "";
	document.getElementById('author'+ num + 'id' + citation_suffix).value = "";
}

// moveAuthorUp	- Used by printAuthorRow()
//				- Move author entry up
Page.moveAuthorUp = function(num, citation_suffix)
{
	if(num == 0)
	{
		Page.swapAuthor(0,5, citation_suffix);	
	}
	else
	{
		Page.swapAuthor(num,num-1, citation_suffix);
	}
}

// moveAuthorDown	- Used by printAuthorRow()
//					- Move author entry down
Page.moveAuthorDown = function(num, citation_suffix)
{
	if(num == 5)
	{
		Page.swapAuthor(5,0, citation_suffix);
	}
	else
	{
		Page.swapAuthor(num,num + 1, citation_suffix);
	}		
}

// swapAuthor	- Used by moveAuthorUp() and moveAuthorDown()
//				- Swap two different entries
Page.swapAuthor = function(num1, num2, citation_suffix)
{
	var temp_ln = document.getElementById('author'+ num1 + 'ln' + citation_suffix).value;
	var temp_fn = document.getElementById('author'+ num1 + 'fn' + citation_suffix).value;
	var temp_id = document.getElementById('author'+ num1 + 'id' + citation_suffix).value;
	
	document.getElementById('author'+ num1 + 'ln' + citation_suffix).value = document.getElementById('author'+ num2 + 'ln' + citation_suffix).value;
	document.getElementById('author'+ num1 + 'fn' + citation_suffix).value = document.getElementById('author'+ num2 + 'fn' + citation_suffix).value;
	document.getElementById('author'+ num1 + 'id' + citation_suffix).value = document.getElementById('author'+ num2 + 'id' + citation_suffix).value;
	
	document.getElementById('author'+ num2 + 'ln' + citation_suffix).value = temp_ln;
	document.getElementById('author'+ num2 + 'fn' + citation_suffix).value = temp_fn;
	document.getElementById('author'+ num2 + 'id' + citation_suffix).value = temp_id;
}

// printTableRowRadio	- Used by nextEntry()
// 						- Generate <tr></tr> for table
//							but with radio buttons entry
Page.printTableRowRadio = function(_citation, name, value)
{
	
	var checked_value = 0;
	if (_citation != "") {
	//if(_citation.getElementsByTagName(value)[0] != null) {
		checked_value = _citation[value];
	}
	if(checked_value == 1)
	{
		return "<tr align=\"left\"><td><b>"+name+": </b></td><td>" + Page.printRadioButton(value, 'Yes', '1', 'checked', 'No', '0', '') + "</td></tr>";		
	}
	else
	{
		return "<tr align=\"left\"><td><b>"+name+": </b></td><td>" + Page.printRadioButton(value, 'Yes', '1', '', 'No', '0', 'checked') + "</td></tr>";
	}	
}

Page.printTableRowCheckBox = function(_citation, name, value, disabled)
{
	var checked_value = 0;
	if (_citation != "") {
		checked_value = _citation[value];
	}
	if(checked_value == 0)
	{
		return (disabled == "disabled") ? (Page.printCheckBox(value, '0', '', disabled) + ' ' + name) : (Page.printCheckBox(value, '0', '') + ' ' + name); 
	}
	else
	{
		return (disabled == "disabled") ? (Page.printCheckBox(value, '1', 'checked', disabled) + ' ' + name) : (Page.printCheckBox(value, '1', 'checked') + ' ' + name); 		
	}	
}

// printInputTableRowRadio	- Used by inputFields()
// 							- Generate <tr></tr> for table
//								but with radio buttons entry
Page.printInputTableRowRadio = function(name, value)
{
	return "<tr align=\"left\"><td>"+name+": </td><td>" + Page.printRadioButton(value, 'Yes', '1', 'checked', 'No', '0', '') + "</td></tr>";	
}

// printRadioButton	- Used by printTableRowRadio() & printInputTableRowRadio()
Page.printRadioButton = function(group, name1, value1, checked1, name2, value2, checked2)
{
	var html = '<input type="radio" id="' + group +'" name="' + group +'" value="' + value1 + '" ' + checked1 + '/>' + name1 
				+ '<input type="radio" id="' + group +'" name="' + group +'" value="' + value2 + '" ' + checked2 + '/>' + name2 + '';
				
	return html;
}

Page.printInputTableRowAutocomplete = function(label, name, value, size)
{
	return "<tr align='left' valign='top'><td><b>"+label+": </b></td><td>" + Page.printTextBoxAutocomplete(name, '', size) + "</td></tr>";
}

// printTextBoxAutocomplete	- Print textbox with autocomplete
Page.printTextBoxAutocomplete = function(name, value, size)
{
	var value_as_text = '';
	var background_str = '';
	if (value == undefined) value = '';
	else {

			if (name.indexOf("_a") >= 0)
			{
				value_as_text = value;
			}
	}
	
	var field_type = 'text';
	if (name.indexOf("_a") >= 0)
	{
		field_type = 'hidden';
	}
	var autocompleteHTML = '';
	if (name.indexOf("_a") < 0)
	{
		autocompleteHTML += '<div align="left" style="vertical-align:middle; height:22px; z-index:' + Page.zCounter-- + '; width:' + size +'em" id="myAutoComplete">';   
	}
	
	if (value == 'HIGHLIGHT_BACKGROUND')
	{
		background_str = ' style="background-color:yellow" ';
		value = '';
	}
	
	autocompleteHTML += '<input type="' + field_type + '" name="' + name + '" id="' + name + '" value="' + value + '" size="' + size + '" ' + background_str + '>';
	if (name.indexOf("_a") < 0)
	{
		autocompleteHTML += '<div align="left" style="vertical-align:middle" id="autocomplete_' + name + '" style="background-color:yellow;" ></div>';   
		autocompleteHTML += '</div>';
	}
	autocompleteHTML += value_as_text + '\n';  
	return autocompleteHTML;
}

Page.tableSpacing = function(citation_suffix)
{
	html = '';
	if (((citation_suffix.indexOf("_a") >= 0)) || (citation_suffix.indexOf("_b") >= 0)) 
	{
		html += '<div style="height:3px"></div>';
	}
	else
	{ 
		html += '<br>';
	}
	
	html = '<br>';
	
	return '';
}

/*Page.printTableRowAutocomplete = function(_citation, label, name, size, citation_suffix)
{	
	var textBoxValue = "";
//	if (_citation.length > 0) {  // might need to define a length function
		if (_citation[name] != 'undefined')
		{
			textBoxValue = _citation[name];
		}
//	}
	var radio_button = '';
	if (citation_suffix != "" && citation_suffix != undefined)
	{
		if (citation_suffix.indexOf("_b") >= 0)
		{
			radio_button += '<input type="radio" name="' + name + '_radio" id="' + name + '_radio" value="' + citation_suffix + '"/>';
		}
		else 
		{
			radio_button += '<input type="radio" name="' + name + '_radio" id="' + name + '_radio" value="' + citation_suffix + '" checked="checked"/>';
		}
	}

	return '<tr align="left" valign="top"><td>Z' + radio_button + '<b>'+label+': </b></td><td>' + Page.printTextBoxAutocomplete(name+citation_suffix, textBoxValue, size) + '</td></tr>';
}*/

Page.highlightRequiredInputFields = function(pubtype)
{
	// Check for first author
	var author0ln = "";
	var author0fn = "";
	
	if(document.getElementById("author0ln") != undefined) 
		document.getElementById("author0ln").style.backgroundColor = (document.getElementById("author0ln").value == "") ? 'yellow' : '';
	
	if(document.getElementById("author0fn") != undefined) 
		document.getElementById("author0fn").style.backgroundColor = (document.getElementById("author0fn").value == "") ? 'yellow' : '';
	
	if(Page.pubtypes_json[pubtype] == undefined) pubtype = "misc";
	
	for(var i in Page.pubtypes_json[pubtype].apa_required_fields)
	{		
		var required_field = Page.pubtypes_json[pubtype].apa_required_fields[i];
		
		if(document.getElementById(required_field) != undefined)
		{
			document.getElementById(required_field).style.backgroundColor = (document.getElementById(required_field).value == "") ? 'yellow' : '';
		}
	}
}

// printTextareaRow	- Used by editFacultyInfoForm
Page.printTextareaRow = function(label, name, value, rows, cols)
{
	var html = "<tr align=\"left\"><td>"+label+": </td><td>";
		html += "<textarea id='" + name +"' name='" + name +"' ";
		html += "rows='"+rows+"' cols='"+cols+"'>"+value+"</textarea></td></tr>";
	return html;
}


Page.printTableRow = function(label, field_name, value, size, citation_suffix, autocomplete_flag, pubtype)
{
	label = '<b>'+ label +': </b>';
	var value_as_text = '';
	
	if (value == 'unknown')
	{
		value = '';
	}

	if(value == undefined) value = '';
	else {
		var move_right_button = '';

		if (citation_suffix != undefined)
		{
			if (citation_suffix.indexOf("_a") >= 0)
			{
				value_as_text = value;
				autocomplete_flag = 0;
			}
		}
		
		// Use Page.highlightRequiredInputFields() instead of codes below
		/*if ((value == '') && (pubtype != 'unknown'))
		{
			//alert(pubtype);
			for (var i=0; i < Page.pubtypes_json[pubtype].apa_required_fields.length; i++)
			{
				if (Page.pubtypes_json[pubtype].apa_required_fields[i] == field_name)
				{
					value = 'HIGHLIGHT_BACKGROUND';
					break;
				}
			}
		}*/
		var html = '';
		html += '<tr height="26px" align="left" style="vertical-align:middle"><td>' + label + '</td><td>';
		if (autocomplete_flag == 1)
		{
			html += Page.printTextBoxAutocomplete(field_name+citation_suffix, value, size) + '</td>';	
		}
		else
		{
			html += Page.printTextBox(field_name+citation_suffix, value, size) + value_as_text + '</td>';
		}
		html += '</tr>';
		return html;
	}	
}

// printTextBox	- Simply print textbox
Page.printTextBox = function(name, value, size)
{
	var html = '';
	var field_type = 'text';
	var background_str = '';
	if (name.indexOf("_a") >= 0)
	{
		field_type = 'hidden';
	}
	
	if (value == 'HIGHLIGHT_BACKGROUND')
	{
		background_str = ' style="background-color:yellow" ';
		value = '';
	}

	if (value == undefined)
	{
		value = "";
	}	
	html += '<input size="' + size +'" type="' + field_type + '" id="' + name +'" name="' + name +'" value="' + value + '" ' + background_str + '>';
	return html;
}


Page.printTableTop = function(table_id, citation_suffix)
{
	var html = '';
	
	var full_table_id = table_id + '_table' + citation_suffix;
	if(citation_suffix == 'buttons')  // Print button tables
	{	
		full_table_id = table_id + '_table_buttons';
	}
	else  // Print regular tables
	{
		full_table_id = table_id + '_table' + citation_suffix;
	}
	//html += '<table id="' + full_table_id + '"  style="border: 2px solid #7D110C" width="100%">';
	html += '<table id="' + full_table_id + '" width="100%">';

	return html;
}

