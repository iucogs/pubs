<?php
	// Testing Lucene.
	function luceneBuildIndex()
	{
		// Create the index
		$index = new Zend_Search_Lucene('./tmp/index', true);
		
		// Grab from database and add it to index.
		$doc = new Zend_Search_Lucene_Document();
	
		// TO-DO: Add id, jTitle and jName.
		$doc->addField(Zend_Search_Lucene_Field::Keyword('link', "Pertama"));
		$doc->addField(Zend_Search_Lucene_Field::Text('title', 	"Nama"));
		$doc->addField(Zend_Search_Lucene_Field::Unstored('contents', "Nubli"));
		
		// Add documents to index.
		$index->addDocument($doc);
		
		$index->commit(); // Commit the index.
		
		echo "<b>Index Built!!</b><br />";	
	}
	
	function luceneSearch()
	{
		//open the index
		$index = new Zend_Search_Lucene('./tmp/index');
		
		$query = 'nama';
		
		$hits = $index->find($query);
		
		echo "Index contains ".$index->count()." documents. <br /><br />";
		
		echo "Search for '".$query."' returned " .count($hits). " hits <br /><br />";
		
		foreach ($hits as $hit) {
			echo $hit->title."<br />";
			echo "\tScore: ".sprintf('%.2f', $hit->score)."<br />";
			echo "\t".$hit->link."<br /><br />";
		}
	}

?>