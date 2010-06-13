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
	 *
	 * @access public
	 * @return string
	 */
	public static function remove_inline_comments($string)
	{
		 return preg_replace('#($|\s)//.*$#Umsi', '', $string);
	}

	/**
	 * Compresses down the CSS file. Not a complete compression,
	 * but enough to minimize parsing time.
	 *
	 * @return string $css
	 */	
	public static function compress($string)
	{		
		# Remove comments
		$string = self::remove_comments($string);
		
		# Remove inline comments
		$string = self::remove_inline_comments($string);
		
		# Extra spaces
		$string = self::remove_extra_whitespace($string);
		
		return $string;
	}

	/**
	 * Removes extra whitespace
	 *
	 * @access public
	 * @param $string
	 * @return string
	 */
	public static function remove_extra_whitespace($string)
	{
		# Remove extra white space
		$string = preg_replace('/\s+/', ' ', $string);
		
		# Remove line breaks
		$string = preg_replace('/\n|\r/', '', $string);
		
		return $string;
	}

	/**
	 * Removes css comments
	 *
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
	 *
	 * @access public
	 * @param $name
	 * @param $string
	 * @return array
	 */
	public static function find_functions($name,$string)
	{
		$regex =
		"/
			{$name}
			(
				\s*\(\s*
					( (?: (?1) | [^()]+ )* )
				\s*\)\s*
			)
		/sx";

		if(preg_match_all($regex, $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}

	/**
	 * Finds @groups within the css and returns
	 * an array with the values, and groups.
	 *
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
			(?:
				\(( [^)]*? )\)
			)?
			
			[^{]*?

			(
				([0-9a-zA-Z\_\-\@*&]*?)\s*
				\{	
					( (?: [^{}]+ | (?2) )*)
				\}
			)

		/ixs";
			
		if(preg_match_all($regex, $string, $matches))
		{
			return $matches;		
		}
		
		return array();
	}
	
	/**
	 * Takes a string of a CSS rule:
	 *
	 * 		property:value;
	 * 		property:value;
	 *		etc.
	 *
	 * And breaks it into an array like so:
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
	 *
	 * @author Anthony Short
	 * @param $css
	 * @param $property string
	 * @param $value string
	 */
	public function find_selectors_with_property($property, $value = ".*?")
	{		
		if(preg_match_all("/([^{}]*)\s*\{\s*[^}]*(".$property."\s*\:\s*(".$value.")\s*\;).*?\s*\}/sx", $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Finds all properties with a particular value
	 *
	 * @author Anthony Short
	 * @param $property
	 * @param $value
	 * @param $css
	 * @return array
	 */
	public function find_properties_with_value($property, $value = ".*?")
	{		
		# Make the property name regex-friendly
		$property = Scaffold_Utils::preg_quote($property);
		$regex = "/ ({$property}) \s*\:\s* ({$value}) /sx";
			
		if(preg_match_all($regex, $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}
		
	/**
	 * Finds a selector and returns it as string
	 *
	 * @author Anthony Short
	 * @param $selector string
	 * @param $css string
	 */
	public function find_selectors($selector, $recursive = "")
	{		
		if($recursive != "")
		{
			$recursive = "|(?{$recursive})";
		}

		$regex = 
			"/
				
				# This is the selector we're looking for
				({$selector})
				
				# Return all inner selectors and properties
				(
					([0-9a-zA-Z\_\-\*&]*?)\s*
					\{	
						(?P<properties>(?:[^{}]+{$recursive})*)
					\}
				)
				
			/xs";
		
		if(preg_match_all($regex, $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Finds all properties within a css string
	 *
	 * @author Anthony Short
	 * @param $property string
	 * @param $css string
	 */
	public function find_property($property)
	{ 		
		if(preg_match_all('/[^-a-zA-Z](('.Scaffold_Utils::preg_quote($property).')\s*\:\s*(.*?)\s*\;)/sx', $this->string, $matches))
		{
			array_shift($matches);
			return $matches;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Check if a selector exists
	 *
	 * @param $name
	 * @return boolean
	 */
	public function selector_exists($name)
	{
		return preg_match('/'.preg_quote($name).'\s*?({|,)/', $this->string);
	}
		
	/**
	 * Removes all instances of a particular property from the css string
	 *
	 * @author Anthony Short
	 * @param $property string
	 * @param $value string
	 * @param $css string
	 */
	public function remove_properties_with_value($property,$value)
	{
		return preg_replace('/'.$property.'\s*\:\s*'.$value.'\s*\;/', '', $this->string);
	}
}