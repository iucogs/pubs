<?php  
	//******************//
	// Common Functions //
	//******************//
	
	define("REGEX_ESCAPE_CHAR_LIST","/)('\"[]?");						// List of characters to be escaped when using addcslashes() to prepare a regex expression.
	define("WORD_COUNT_MASK", "/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u"); // Used by str_word_count_utf8.
	define("WORD_DEF", "\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*"); // Used by str_word_count_utf8.
	
	function removeYearAlphabetAtStart($str)
	{
		$pattern = '/^[^\p{L}]*(\p{Ll}\p{Pe})[,]*/u'; // ^[^any_letter]*(lower_letter close_bracket)[comma]*
		$str = preg_replace($pattern, '', $str);
		return $str;
	}
	
	function clean($str)										
	{	
		$start_pattern = '/^[^\p{L&}]*/u';					// Clean the beginning of a string everything that are not letters
		$end_pattern = '/[^\??\p{L&}]*$/u';					// Remove everything that are not letters and ?				

		// Regular function with /u for UTF8 support
		$str = preg_replace($start_pattern,"", $str, 1);	// preg_replace removes everything when limit is default/-1 and the first char is unicode accented char! use limit 1.			
		$str = preg_replace($end_pattern,"", $str, 1);		// preg_replace removes everything when limit is default/-1 and the first char is unicode accented char! use limit 1.			
		return $str;
	}
	
	function checkUTF8($str)
	{
		$input = $str;
		if (($input = @iconv('UTF-8', 'UTF-8', $str)) == $str) echo "Good UTF-8!<br />"; else echo "Nope.<br />";
		
		//echo "line strlen: <b>".strlen($line)."</b> $line<br />";
		//echo "line mb_strlen: <b>".mb_strlen($line)."</b> $line<br />";
		//echo "line strlen: <b>".strlen($line)."</b> $line<br />";
		//echo "line mb_strlen: <b>".mb_strlen($line)."</b> $line<br />";
		
		echo "str strlen: <b>".strlen($str)."</b> $str<br />";
		echo "input strlen: <b>".strlen($input)."</b> $input<br />";
		echo "str mb_strlen: <b>".mb_strlen($str)."</b> $str<br />";
		echo "input mb_strlen: <b>".mb_strlen($input)."</b> $input<br />";
	}
	
	function print_r_html($str)
	{
		echo "<pre>"; print_r($str); echo "</pre>";
	}
	
	function echo_html($str)
	{
		echo $str."<br />";	
	}
	
	function echo_html_name($str, $name)
	{
		echo "<b>".$name."</b>: ".$str."<br />";
	}
	
    function str_word_count_utf8($str, $format)  // Format 1 = return the words in array, else return number of words.
    {
        if($value = preg_match_all(WORD_COUNT_MASK, $str, $matches))
		{
			if($format == 1) return $matches[0];
			else return $value;
		}
    }
	
?>