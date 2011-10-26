<?php

	function parseURL($str)
  	{
		$pattern = "{
					  \\b
					  # Match the leading part (proto://hostname, or just hostname)
					  (
						# http://, or https:// leading part
						(https?)://[-\\w]+(\\.\\w[-\\w]*)+
					  |
						# or, try to find a hostname with more specific sub-expression
						(?i: [a-z0-9] (?:[-a-z0-9]*[a-z0-9])? \\. )+ # sub domains
						# Now ending .com, etc. For these, require lowercase
						(?-i: com\\b
							| edu\\b
							| biz\\b
							| gov\\b
							| in(?:t|fo)\\b # .int or .info
							| mil\\b
							| net\\b
							| org\\b
							| [a-z][a-z]\\.[a-z][a-z]\\b # two-letter country code
						)
					  )
					
					  # Allow an optional port number
					  ( : \\d+ )?
					
					  # The rest of the URL is optional, and begins with /
					  (
						/
						# The rest are heuristics for what seems to work well
						[^.!,?;\"\\'<>()\[\]\{\}\s\x7F-\\xFF]*
						(
						  [.!,?]+ [^.!,?;\"\\'<>()\\[\\]\{\\}\s\\x7F-\\xFF]+
						)*
					  )?
					}ix";
		
		$url = "";
		if(preg_match($pattern, $str, $match) == 1)	{ // Look for URL
			$url = $match[0];
		}
		else {
			$url = "";
		}		
		return $url;
	}
?>