===================
 Parse.php Outline
===================

- Main loop is in here.
- Includes -> parseDate.php, parseAuthor.php, parseType.php, parseJournal.php, parseBibtex.php.
- Open a file for input.
- "data" array holds all entries.
- "entry" dictionary holds all the information on each entry.
- For each entry, "raw" entry is being save first.
- Then, the raw entry is passed to parseDate.
- Take the first year and split the raw entry.
- First part of the string will most likely contains the authors name.
- Then, the string will be passed to parseAuthor.
- The rest of the string will be sent to parseType to search for type, title and so on.

=================
 parseDate.php
=================

- Fix for year followed by colon.
- Remove patterns with numbers that are not year.
- Year Pattern: 4 digits | 3 digits + ? | (in press) | (forthcoming).
- Check for year between 1400 (printing press 1440) - current year plus one.
- Check the number of years found.

=================
 parseAuthor.php
=================

- Prepare text string for processing by:
	- Remove multiple spaces
	- Remove Edition: eds. | et al. | ed.
	- Remove ,& / ,and
	- Remove & / and
	- Remove unusual characters 
- Generic patterns to identify names:
	- Generic form "Name, X. X.,"
	- Generic form "X. X. Name,"
	- Generic form "Name, X. X."
	- Generic form "X. X. Name"
- Loop until $value string is empty
- Catch normal form "Name, X. X.,"
	- Catch regular last name "Name,"
	- Catch compound last name "Name-Name"
	- Catch split last name "Name Name"
	- Catch Mc's, Mac's and other names with two capital letters
- Catch "X. X. Name," and convert to "Name, X. X.,"
- Catch end of list "Name, X. X." and add comma to end
- Catch end of list "X. X. Name" and add comma to end
- No regular patterns are found - look for unusual patterns or remove first character and loop
	- Check for leftover Jr, Sr, or III and add it to previous name
	- Check for no caps last name
		- Find last name and capitalize first letter
- Helpers: 
	- Move Last name to front of intitials
	- Formats initials with correct periods and spacing

===============
 parseType.php
===============

- Check for journal entry.
	1. Patterns
		- volume:page-page
		- volume(number)
		- without "eds"
	2. Search Journal Name from DB
- Check for other types.

==================
 parseJournal.php
==================

- Try pattern matching.
- Try finding the obvious ones first
	1. Check for obvious (?) Don't matter how many (.) there are.
	2. Check if string has more than one (.)
	3. Then, check for (.)
	4. Else, later. (undecided)

=================
 parseBibtex.php
=================

- Print out everything in bibtext format from data array.

