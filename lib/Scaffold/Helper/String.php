<?php

/**
 * Scaffold_Helper_String
 *
 * Helper methods for dealing with string
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Helper_String
{
	/**
	 * Get the line number of a certain string within the CSS
	 * @access public
	 * @param $string
	 * @return return type
	 */
	public function line_number($find,$string)
	{	
		if(strstr($string,$find))
		{
			$before = explode($find,$string);
			$lines = explode("\n",$before[0]);
			$line = count($lines);
			return $line;
		}
		return false;
	}
	
	/**
	 * Get the contents of a line number
	 * @access public
	 * @param $line int
	 * @param $string string
	 * @param $content boolean
	 * @return return type
	 */
	public function line_contents($line,$string,$context = false)
	{
		$string = explode("\n",$string);
		
		// Just return this line
		if($context === false)
			return $string[$line - 1];
		
		// Or return lines around it
		$lines = array();
		
		// The start of the string
		$min = $line - $context;
		if($min < 0) $min = 0;
		
		// The end of the string
		$max = $line + $context;
		if($max > (count($string) -1)) $max = count($string) - 1;
		
		for ($i = $min; $i <= $max; $i++)
			$lines[] = $string[$i];
		
		return implode("\n",$lines);
	}
	
	/**
	 * Matches a custom delimiter in a string and returns the contents
	 * @access public
	 * @param $open The opening delimiter character
	 * @param $close The closing delimiter character
	 * @param $first The position of the first opening character in the string
	 * @param $string
	 * @return $string
	 */
	public function match_delimiter($open,$close,$first,$string)
	{
		$start = $pos = $first + 1;
		$depth = 0;
		$content = false;
		
		while($content == false)
		{
			$current = $string[$pos];

			// We've found the closing brace
			if($current == $close AND $depth == 0)
			{
				$content = substr($string,$first,$pos - $first + 1);
			}
			
			// We've closed a nested brace			
			if($current == $close) $depth--;

			// There's a nested brace
			if($current == $open) $depth++;	

			// We've reached the end of the string
			if($pos == strlen($string))
				throw new Exception('Unmatched ' . $open);
	
			// Go to the next character
			$pos++;
		}
		
		return $content;
	}
}