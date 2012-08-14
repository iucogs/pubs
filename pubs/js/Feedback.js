var cogs_value;

Page.get_feedback_request = function(cogs)
{
	cogs_value=cogs;
	var jsonStr = '{"request": {"type": "get_feedback_list", "submitter": "' + Page.submitter + '"}}';
	Ajax.SendJSON('services/feedback.php', Page.get_feedback_response, jsonStr);
}

//Abhinav 

Page.get_sortedfeedback_request = function(cogs)
{
	cogs_value=cogs;
	var jsonStr = '{"request": {"type": "get_sortedfeedback_list", "submitter": "' + Page.submitter + '"}}';
	Ajax.SendJSON('services/feedback.php', Page.get_feedback_response, jsonStr);
}


Page.get_feedback_response = function()
{						
	html = '';
	
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		//alert(Ajax.request.responseText);
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		
		if (responseObj.feedback_list != undefined)
		{
			Page.feedback_list = responseObj.feedback_list;

			html += '<br><br><textarea id="add_feedback" rows="5" cols="100"></textarea><br>';
			html += '<input type="button" id="add_feedback_button" value="Add feedback" onclick="Page.add_feedback_request();"><br><br>';
		}
	}
	
	html += '<div id="feedback_table_div"></div>';	
	

	document.getElementById('feedback_div').innerHTML = html;

	Page.write_feedback_table('list');
}

Page.write_feedback_table = function(list_or_editID)
{
	var html = '';
	html += '<br><br><table border="1">';

			
	html += '<tr valign="top">';
	html += '<td><b>ID</b></td>';
	html += '<td class="pointerhand" onclick="Page.adminPage(1,\''+ cogs_value +'\')";><b>Suggested By</b></td>'; //Abhinav
	html += '<td><b>Date</b></td>';
	html += '<td><b>Feedback</b></td>';
	html += '<td><b>Fixed</b></td>';
	//html += '<td><b>Date Fixed</b></td>';
	html += '<td><b>Comment</b></td>';
	html += '<td><b>Edit</b></td>';
	html += '<td><b>Delete</b></td>';
	html += '</tr>';
	
	
	for (var i=0; i < Page.feedback_list.length; i++)
	{
		html += '<tr valign="top">';
		html += '<td>' + Page.feedback_list[i].id + '</td>';
		html += '<td>'+ Page.feedback_list[i].submitter + '</td>';
		
		var date = new Date(Page.feedback_list[i].date_submitted*1000);
		var month = date.getMonth()+1;
		html += '<td>'+ month + '-' + date.getDate() + '-' + date.getFullYear()  + '</td>';
		if (Page.feedback_list[i].id == list_or_editID)
		{
			html += '<td><textarea cols="75" id="update_feedback_' + Page.feedback_list[i].id + '">'+ Page.feedback_list[i].bug + '</textarea></td>';
		}
		else		
		{
			html += '<td>'+ Page.feedback_list[i].bug + '</td>';
		}
		
		html += '<td><input type="checkbox" id="bug_fixed_cb_' + Page.feedback_list[i].id + '" ';
		if (Page.feedback_list[i].bug_fixed == 1)
		{
			html += 'checked="checked"';
		}
		if (Page.feedback_list[i].id != list_or_editID)
		{
			html += ' disabled="disabled"';
		}
		html += '></td>';
	//	html += '<td>'+ Page.feedback_list[i].date_fixed + '</td>';

		if (Page.feedback_list[i].id == list_or_editID)
		{
			html += '<td><textarea cols="35" id="update_comment_' + Page.feedback_list[i].id + '">'+ Page.feedback_list[i].comment + '</textarea></td>';
		}
		else		
		{
			html += '<td>'+ Page.feedback_list[i].comment + '</td>';
		}

		if (Page.feedback_list[i].id == list_or_editID)
		{
			html += '<td><input type="button" id="update_feedback_button_' + Page.feedback_list[i].id + '" value="Submit" onclick="Page.update_feedback_request(this.id);"></td>';
		}
		else
		{
			html += '<td><input type="button" id="edit_feedback_button_' + Page.feedback_list[i].id + '" value="Edit" onclick="Page.edit_feedback(this.id);"></td>';
		}
		
		html += '<td><input type="button" id="delete_feedback_button_' + Page.feedback_list[i].id + '" value="Delete" onclick="Page.delete_feedback_request(this.id);"></td>';
		html += '</tr>';				
	}
	
	
	html += '</table>';
	document.getElementById('feedback_table_div').innerHTML = html;
}

Page.edit_feedback = function(id)
{
	var temp_array = id.split('_');
	Page.write_feedback_table(temp_array[3]);	
}

Page.update_feedback_request = function(id)
{
	var temp_array = id.split('_');
	var feedback_to_update = document.getElementById('update_feedback_'+temp_array[3]).value; 
	var comment_to_update = document.getElementById('update_comment_'+temp_array[3]).value; 
	var bug_fixed_to_update = 0; 
	if (document.getElementById('bug_fixed_cb_'+temp_array[3]).checked == true)
	{
		bug_fixed_to_update = 1;
	}
	var jsonStr = '{"request": {"type": "update_feedback", "submitter": "' + Page.submitter + '", "feedback": ' + YAHOO.lang.JSON.stringify(feedback_to_update) + ', "comment": ' + YAHOO.lang.JSON.stringify(comment_to_update) + ', "bug_fixed": ' + YAHOO.lang.JSON.stringify(bug_fixed_to_update) + ', "id": "' + temp_array[3] + '"}}';
	Ajax.SendJSON('services/feedback.php', Page.get_feedback_response, jsonStr);	
}

Page.add_feedback_request = function()
{
	var jsonStr = '{"request": {"type": "add_feedback", "submitter": "' + Page.submitter + '", "feedback": ' + YAHOO.lang.JSON.stringify(document.getElementById('add_feedback').value) + '}}';
	Ajax.SendJSON('services/feedback.php', Page.get_feedback_response, jsonStr);
}

/*Page.edit_feedback_request = function()
{
	alert('edit');
	var jsonStr = '{"request": {"type": "edit_feedback", "submitter": "' + Page.submitter + '", "feedback": "' + document.getElementById('add_feedback').value + '"}}';
	Ajax.SendJSON('services/feedback.php', Page.get_feedback_response, jsonStr);
}*/

Page.delete_feedback_request = function(id)
{
	if (confirm('Are you sure?'))
	{				
		var temp_array = id.split('_');
		var jsonStr = '{"request": {"type": "delete_feedback", "submitter": "' + Page.submitter + '", "id": "' + temp_array[3] + '"}}';
		Ajax.SendJSON('services/feedback.php', Page.get_feedback_response, jsonStr);	
	}
}



