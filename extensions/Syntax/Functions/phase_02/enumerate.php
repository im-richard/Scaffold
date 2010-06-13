<?php

/**
 * Parses enumerate()'s within the CSS. Enumerate
 * lets you build a selector based on a start and end
 * number. Like columns-1 through columns-12.
 *
 * enumerate('.name-',1,3) { properties }
 *
 * Will produce something like:
 *
 * .name-1, .name-2, .name-3 { properties }
 *
 * @param $string
 * @param $min
 * @param $max
 * @param $sep
 * @return string
 */
function Scaffold_enumerate($string, $min, $max, $sep = ",")
{
	$ret = array();
	$string = Scaffold_Utils::unquote($string);
	
	for ($i = $min; $i <= $max; $i++)
	{
		$ret[] = $string . $i;
	}
	
	return implode($sep, $ret);
}