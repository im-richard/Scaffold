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
	 * Valid start of a selector
	 * @var string
	 */
	public $selector_start = '[0-9A-Za-z\_\#\.\*\:\&\=]';

	/**
	 * Regex for a valid selector
	 * @var string
	 */
	public $selector = '[0-9a-zA-Z\_\-\*\&\#\[\]\~\=\|\"\'\^\$\:\>\+\(\)\.\s\,]*';
	
	/**
	 * Regex for a block
	 * @var string
	 */
	public $block = '\{([^}]*)\}';
	
	/**
	 * Create a regex string from a string of pseudo-regex. Yep.
	 * String variables that can be used:
	 *
	 * - IDENTIFIER
	 * - BLOCK
	 * 
	 * @access public
	 * @param string
	 * @return string
	 */
	public function create_regex($str)
	{
		$str = str_replace(" ", '\s+', $str);
		$str = str_replace('IDENTIFIER', $this->selector_start . $this->selector, $str);
		$str = str_replace('BLOCK', $this->block, $str);
		return $str;
	}
	 
	/**
	 * Removes single-line comments from a string
	 * @access public
	 * @param string
	 * @return string
	 */
	public function remove_inline_comments($string)
	{
		 return preg_replace('#($|\s)//.*$#Umsi', '', $string);
	}

	/**
	 * Removes line breaks and tabs
	 * @access public
	 * @param $string
	 * @return string
	 */
	public function remove_newlines($string)
	{
		return preg_replace('/\n+|\r+|\t+/', '', $string);
	}

	/**
	 * Removes css comments
	 * @access public
	 * @param $string
	 * @return string
	 */
	public function remove_comments($string)
	{
		return preg_replace('#/\*[^*]*\*+([^/*][^*]*\*+)*/#', '', $string);
	}
	
	/**
	 * Encodes a selector so that it's able to be used in regular expressions
	 * @access public
	 * @param $selector
	 * @return string
	 */
	public function escape_regex($selector)
	{
		$selector = preg_quote($selector,'-');
		$selector = str_replace('#','\#',$selector);
		$selector = preg_replace('/\s+/',"\s+",$selector);
		return $selector;
	}
	
	// ============================
	// = Function Methods =
	// ============================

	/**
	 * Finds CSS 'functions'. These are things like url(), embed() etc.
	 * Handles interior brackets as well by using recursion.
	 * Also handles nested functions.
	 * @access public
	 * @param $name
	 * @param $string
	 * @param $type int Which array to return
	 * @return array
	 */
	public function find_functions($name,$string)
	{
		$return = array();
		$regex ="/{$name}(\s*\(\s*((?:(?0)|(?1)|[^()]+)*)\s*\)\s*)/sx";
		
		if(preg_match_all($regex, $string, $match))
		{
			foreach($match[0] as $key => $value)
			{
				$params = $match[2][$key];
				
				// Encode commas in between braces so we can have nested functions
				if(preg_match_all('/\(.*?\,.*?\)/',$match[2][$key],$nested_commas))
				{
					foreach($nested_commas[0] as $key => $commas)
					{
						$replace = str_replace(',','#COMMA#',$commas);
						$params = str_replace($commas,$replace,$params);
					}
				}
				
				// Break the params into an array
				$params = explode(',',$params);
				
				// Decode commas
				foreach($params as $param_key => $param)
				{
					$params[$param_key] = str_replace('#COMMA#',',',$param);
				}
				
				$return[] = array(
					'string' => $value,
					'params' => $params
				);
				
			}
		}
		
		return $return;
	}
	
	// ============================
	// = Ruleset Methods =
	// ============================
	
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
	public function ruleset_to_array($string)
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
				$return[trim($value[0])] = str_replace('#COLON#', ':', trim($value[1]));
			}
		}
		
		return $return;
	}
	
	// ============================
	// = Selector Methods =
	// ============================
	
	/**
	 * Finds selectors which contain a particular property
	 * @access public
	 * @param $property string
	 * @param $css string
	 * @return array
	 */
	public function find_selectors_with_property($property,$css)
	{
		$return = array();
		$regex = "/([^{}]*)\s*\{\s*($property|[^}]*[\s\;]$property)\s*:[^}]*}/sx";
		
		if(preg_match_all($regex,$css,$match))
		{
			foreach($match[0] as $key => $value)
			{
				$return[$key] = array(
					'string' => $value,
					'selector' => $match[1][$key]
				);
			}
		}
		
		return $return;
	}
	
	/**
	 * Finds a selector and returns it as string
	 * @access public
	 * @param $selector string
	 * @param $string string
	 * @todo This will break if the selector they try and find is actually part of another selector
	 */
	public function find_selectors($selector,$string,$escape = true)
	{
		$selector = ($escape) ? self::escape_regex($selector) : $selector;
		$regex = "/(^|[\}\s\;])* ( ($selector) \s*\{[^}]*\} )/sx";
		return preg_match_all($regex, $string, $match) ? $match[2] : array();
	}

	/**
	 * Check if a selector exists
	 * @param $name
	 * @param $string
	 * @return boolean
	 */
	public function selector_exists($name,$string)
	{
		return preg_match('/(^|})\s*'.$name.'\s*\{/', $string) ? true : false;
	}
	
	/** 
	 * Checks if a selector is valid
	 * @param $string
	 * @return boolean
	 */
	public function valid_selector($string)
	{
		return preg_match(self::$_identifier,$string);
	}
	
	// ============================
	// = Property Methods =
	// ============================
	
	/**
	 * Finds all properties with a particular value
	 * @access public
	 * @param $property Regex formatted string for a property
	 * @param $value Regex formatted string for a value
	 * @param $css
	 * @return array
	 */
	public function find_properties_with_value($property,$value,$css,$escape_value=true)
	{
		$return = array();
		$escaped_property = self::escape_regex($property);
		$value = $escape_value ? self::escape_regex($value) : $value;
		
		$regex = "/
			\s*
			
			([^{}]*)									(?# selector)
			
			\s*
			
			\{		
													
				(?:										(?# property is complicated, as we may be first or intermediate - dbackground instance)
					[^}]*[\s\;]
					|
					\s*
				)
				($escaped_property\s*)	
				
				:										(?# property value seperator)
				
				(\s*$value\s*;?)
				
				
				[^}]*									(?# everything after our property of interest)
				
			\}											(?# end content)
			
			/sx";
		
		if(preg_match_all($regex,$css,$matches,PREG_SET_ORDER))
		{
			foreach ( $matches as $key => $match )
			{			
				$return[$key] = array(
					'string' => trim($match[0]),
					'selector' => $match[1],
					'property' => $match[2].':'.$match[3],
					'value' => trim($match[3],':; ')
				);
			}
		}
		
		return $return;
	}
		
	/**
	 * Removes all instances of a particular property from the css string
	 * @access public
	 * @param $property string
	 * @param $value string
	 * @param $css string
	 */
	public function remove_properties_with_value($property,$value,$string,$escape_value=true)
	{
		# Prepare
		$escaped_property = self::escape_regex($property);
		$value = $escape_value ? self::escape_regex($value) : $value;
		
		# Generate regex
		$regex = "/
			(
				\{							
				(?:										(?# property is complicated, as we may be first or intermediate - dbackground instance)
					\s*
					|
					[^}]*[\s\;]
				)
			)
			
			($escaped_property\s*)	
			
			:										(?# property value seperator)
			
			(\s*$value\s*;?)
			
		
		/sx";
		
		# Remove property
		$string = preg_replace($regex, '$1', $string);
		
		# Return string
		return $string;
	}
	
	/**
	 * Finds all properties within a css string
	 * @access public
	 * @param $property string Regex formatted string
	 * @param $string string
	 */
	public function find_properties($property,$string)
	{
		return self::find_properties_with_value($property,'[^;}]*',$string,false);
	}
	
	/**
	 * Removes all instances of a particular property from the css string
	 * @access public
	 * @param $property string
	 * @param $string string
	 */
	public function remove_properties($property,$string)
	{
		return self::remove_properties_with_value($property,'[^;}]*',$string,false);
	}
	
	// ============================
	// = @ Rule Methods =
	// ============================

	/**
	 * Finds @groups within the css and returns
	 * an array with the values, and groups.
	 * @access public
	 * @param $name
	 * @param $string
	 * @return array
	 */
	public function find_atrule($name,$string)
	{
		$name = self::escape_regex($name);
		$regex = "/
		@{$name}			(?# the name to find)
		
		([^{]*?)			(?# atrules params)
		
		\{					(?# start atrule content)
			(.*?)
		\}					(?# end atrule content)
		
		/xs";

		$result = preg_match_all($regex, $string, $matches, PREG_SET_ORDER) ? $matches : array();	
		
		return $result;
	}
	
	/**
	 * Removes an atrule from a CSS string
	 * @access public
	 * @param $name
	 * @param $css
	 * @return string
	 */
	public function remove_atrule($name,$css)
	{
		$rules = self::find_atrule($name,$css);
		return str_replace($rules[0],'',$css);
	}
}