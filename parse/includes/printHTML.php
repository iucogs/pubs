<?php

function common_row($entry)
{
	?> <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Citation ID</td>
            <td align='left'><?php echo $entry['citation_id'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Format</td>
            <td align='left'><?php echo $entry['format'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Type</td>
            <td align='left'><?php echo $entry['type'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Year(s)</td>
            <td align='left'><?php echo implode(", ", $entry['year']) ?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Author(s)</td>
            <td align='left'><?php echo implode(" | ", $entry['author']) ?>&nbsp;</td>
        </tr>
    <?php
}

function proceedings_row($entry)
{
	?>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Title</td>
            <td align='left'><?php echo $entry['title'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Proceeding Name</td>
            <td align='left'><?php echo $entry['name'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Publisher</td>
            <td align='left'><?php echo $entry['publisher'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Location</td>
            <td align='left'><?php echo $entry['location'];?>&nbsp;</td>
        </tr>
    <?php
}

function article_row($entry)
{
	?>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Title</td>
            <td align='left'><?php echo $entry['title'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Journal Name</td>
            <td align='left'><?php echo $entry['name'];?>&nbsp;</td>
        </tr>
    <?php
}

function inbook_row($entry)
{
	?>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Title</td>
            <td align='left'><?php echo $entry['title'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Editors</td>
            <td align='left'><?php echo $entry['editor'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Booktitle</td>
            <td align='left'><?php echo $entry['booktitle'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Publisher</td>
            <td align='left'><?php echo $entry['publisher'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Location</td>
            <td align='left'><?php echo $entry['location'];?>&nbsp;</td>
        </tr>
    <?php
}

function book_row($entry)
{
	?>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Title</td>
            <td align='left'><?php echo $entry['title'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Publisher</td>
            <td align='left'><?php echo $entry['publisher'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Location</td>
            <td align='left'><?php echo $entry['location'];?>&nbsp;</td>
        </tr>
<!--        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Editors</td>
            <td align='left'><?php echo $entry['editor'];?>&nbsp;</td>
        </tr>-->
    <?php
}

function other_row($entry)
{
	?>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Title</td>
            <td align='left'><?php echo $entry['title'];?>&nbsp;</td>
        </tr>
    <?php
}

function volume_row($entry)
{
	?>
    	<tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Volume</td>
            <td align='left'><?php echo $entry['volume'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Chapter</td>
            <td align='left'><?php echo $entry['chapter'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Number</td>
            <td align='left'><?php echo $entry['number'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Pages</td>
            <td align='left'><?php echo $entry['pages'];?>&nbsp;</td>
        </tr>
        <tr>
            <td align='left' bgcolor='#b0c4de' width="12%">URL</td>
            <td align='left'><?php echo $entry['url'];?>&nbsp;</td>
        </tr>
    <?php
}

function printHTML($entry, $count)
{
	?>
	 <table align="center" border="1" width="60%" style="background-color: #fff5ee">
     <tr>
        <td align='left' bgcolor='#b0c4de' width="12%">Count</td>
        <td align='left'><?php echo $count;?>&nbsp;</td>
	</tr>
	<?php

	switch($entry['type'])
	{
		case "proceedings"	:
			common_row($entry);
			proceedings_row($entry);
			volume_row($entry);
			echo "<br />";
			break;
		
		case "article"	:
			common_row($entry);
			article_row($entry);
			volume_row($entry);
			echo "<br />";
			break;
			
		case "inbook"	:
			common_row($entry);
			inbook_row($entry);
			volume_row($entry);
			echo "<br />";
			break;
		case "edited_book"	:	
		case "book"			:
			common_row($entry);
			book_row($entry);
			volume_row($entry);
			echo "<br />";
			break;
			
		default:
			common_row($entry);
			other_row($entry);
			volume_row($entry);
			echo "<br />";		
	}
	
	
	?>
    	<tr>
            <td align='left' bgcolor='#b0c4de' width="12%">Raw</td>
            <td align='left'><?php echo $entry['raw'];?></td>
        </tr>	 
	</table>
    <br>
	<br>

	<?php
}
?>