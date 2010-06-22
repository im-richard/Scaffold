<?php
/**
 * Scaffold_CSS
 *
 * Helper methods for dealing with CSS
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Helper_CSS
{
	/**
	 * Removes single-line comments from a string
	 * @access public
	 * @param string
	 * @return string
	 */
	public static function remove_inline_comments($string)
	{
		 return preg_replace('#($|\s)//.*$#Umsi', '', $string);
	}

	/**
	 * Compresses down the CSS file by removing comments and whitespace
	 * @access public
	 * @param string
	 * @return string $css
	 */	
	public static function compress($string)
	{		
		$string = self::remove_comments($string);
		$string = self::remove_inline_comments($string);
		$string = self::remove_whitespace($string);
		return $string;
	}

	/**
	 * Removes extra whitespace and line breaks
	 * @access public
	 * @param $string
	 * @return string
	 */
	public static function remove_whitespace($string)
	{
		$string = preg_replace('/\s+/', ' ', $string);
		$string = preg_replace('/\n|\r/', '', $string);
		return $string;
	}

	/**
	 * Removes css comments
	 * @access public
	 * @param $string
	 * @return string
	 */
	public static function remove_comments($string)
	{
		return preg_replace('#/\*[^*]*\*+([^/*][^*]*\*+)*/#', '', $string);
	}

	/**
	 * Finds CSS 'functions'. These are things like url(), embed() etc.
	 * Handles interior brackets as well by using recursion.
	 * @access public
	 * @param $name
	 * @param $string
	 * @return array
	 */
	public static function find_functions($name,$string)
	{
		$regex ="/{$name}(\s*\(\s*((?:(?1)|[^()]+)*)\s*\)\s*)/sx";
		return preg_match_all($regex, $this->string, $match) ? $match : array();
	}

	/**
	 * Finds @groups within the css and returns
	 * an array with the values, and groups.
	 * @access public
	 * @param $name
	 * @param $string
	 * @return array
	 */
	public static function find_atrule($name,$string)
	{
		$regex = 
		"/
			# Group name
			@{$name}
			
			# Flag
			(?:\(( [^)]*? )\))?
			
			[^{]*?

			(
				([0-9a-zA-Z\_\-\@*&]*?)\s*		# Selector
				\{	
					( (?: [^{}]+ | (?2) )*)		# Selector Contents
				\}
			)

		/ixs";

		return preg_match_all($regex, $string, $matches) ? $matches : array();		
	}
	
	/**
	 * Removes an atrule from a CSS string
	 * @access public
	 * @param $name
	 * @param $css
	 * @return string
	 */
	public static function remove_atrule($name,$css)
	{
		$rules = self::find_atrule($name,$css);
		return str_replace($rules[0],'',$css);
	}
	
	/**
	 * Takes a string of a CSS rule:
	 *
	 * 		property:value;
	 * 		property:value;
	 *		etc.
	 *
	 * And breaks it into an array:
	 *
	 *		array('property'=>'value');
	 *
	 * @param $string
	 * @return array
	 */
	public static function ruleset_to_array($string)
	{
		$return = array();

		foreach(explode(";", $string) as $value)
		{
			// Encode any colons inside quotations
			if(preg_match_all('/[\'"](.*?\:.*?)[\'"]/',$value,$m) )
			{
				$value = str_replace($m[0][0],str_replace(':','#COLON#',$m[0][0]),$value);
			}

			$value = explode(":", $value);	
			
			// Make sure it's set
			if(isset($value[1]))
			{
				$return[trim($value[0])] = str_replace('#COLON#', ':', trim($value[1], "'\" "));
			}
		}
		
		return $return;
	}
	
	/**
	 * Finds selectors which contain a particular property
	 * @access public
	 * @param $property string
	 * @param $value string
	 * @param $css string
	 * @return array
	 */
	public static function find_selectors_with_property($property,$value,$css)
	{
		$regex = "/([^{}]*)\s*\{\s*[^}]*(".$property."\s*\:\s*(".$value.")\s*\;).*?\s*\}/sx";
		return preg_match_all($regex,$css,$match) ? $match : array();
	}
	
	/**
	 * Finds all properties with a particular value
	 * @access public
	 * @param $property Regex formatted string for a property
	 * @param $value Regex formatted string for a value
	 * @param $css
	 * @return array
	 */
	public static function find_properties_with_value($property,$value,$css)
	{
		$regex = "/({$property})\s*\:\s*({$value})/sx";
		return preg_match_all($regex, $this->string, $match) ? $match : array();
	}
		
	/**
	 * Finds a selector and returns it as string
	 * @access public
	 * @param $selector string
	 * @param $string string
	 * @todo This will break if the selector they try and find is actually part of another selector
	 */
	public static function find_selectors($selector,$string)
	{
		$regex = 
			"/
				# This is the selector we're looking for
				({$selector})
				
				# Return all inner selectors and properties
				(
					([0-9a-zA-Z\_\-\*&]*?)\s*
					\{	
						(?P<properties>(?:[^{}]+|(?2))*)
					\}
				)
			/xs";

		return preg_match_all($regex, $this->string, $match) ? $match : array();
	}
	
	/**
	 * Finds all properties within a css string
	 * @access public
	 * @param $property string Regex formatted string
	 * @param $string string
	 */
	public static function find_property($property,$string)
	{ 
		$regex = '/[^-a-zA-Z](('.$property.')\s*\:\s*(.*?)\s*\;)/sx';
		return preg_match_all($regex,$string,$matches) ? $matches : array();
	}
	
	/**
	 * Check if a selector exists
	 * @param $name
	 * @param string
	 * @return boolean
	 */
	public static function selector_exists($name,$string)
	{
		return preg_match('/'.$name.'\s*?({|,)/', $string) ? true : false;
	}
		
	/**
	 * Removes all instances of a particular property from the css string
	 * @access public
	 * @param $property string
	 * @param $value string
	 * @param $css string
	 */
	public static function remove_properties_with_value($property,$value,$string)
	{
		return preg_replace('/'.$property.'\s*\:\s*'.$value.'\s*\;/', '', $string);
	}
}