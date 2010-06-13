<?php

/**
 * Constants
 *
 * Allows you to use constants within your css by defining them
 * within @constants and then using a property list.
 *
 * @package 		Scaffold
 * @subpackage		Engine
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Engine_Constants
{
	/**
	 * Stores all of the constants as key/value pairs
	 *
	 * @var array
	 */
	public $constants = array();
	
	/**
	 * Parses the @constants rules
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function extract($css)
	{
		$constants = Scaffold_CSS::find_atrule('constants',$css);

		# Create the at rule
		foreach(Scaffold_CSS::ruleset_to_array($constants['ruleset']) as $key => $value)
		{
			# If the value contains already set constants
			$value = $this->replace($value);
			
			# Then set it
			$this->set($key,$value);
		}
	}

	/**
	 * Sets constants. If the first value is an array, it will set all them as constants,
	 * using the keys of the array as the keys of the constants.
	 *
	 * @access public
	 * @param $key mixed
	 * @param $value string
	 * @return void
	 */
	public function set($key, $value = "")
	{
		if(is_array($key))
		{
			foreach($key as $name => $val)
			{
				$this->constants[$name] = $val;
			}
		}
		else
		{
			$this->constants[$key] = $value;
		}
	}
	
	/**
	 * Unsets a constant
	 *
	 * @param $key
	 * @return void
	 */
	public function remove($key)
	{
		unset($this->constants[$key]);
	}
	
	/**
	 * Returns the constant value
	 *
	 * @author Anthony Short
	 * @param $key
	 * @return string
	 */
	public function get($key)
	{
		return $this->$constants[$key];
	}
	
	/**
	 * Replaces all of the constants in a CSS string
	 * with the constants defined in the member variable $constants
	 * using PHP's interpolation.
	 */
	public function replace($css)
	{
		# Pull the constants into the local scope as variables
		extract($this->constants, EXTR_SKIP);
		
		try
		{
			$css = stripslashes( eval('return "' . addslashes($css) . '";') );
		}
		catch(Exception $e)
		{
			$trace = $e->getTrace();
			$missing = str_replace('Undefined variable: ', '',$trace[0]['args'][1]);
			throw new Exception('Missing CSS constant named ' . $missing);
		}
		
		return $css;
	}
	
	/**
	 * Loads constants from an XML file
	 *
	 * @param $param
	 * @return return type
	 */
	public function parse_xml(SimpleXMLElement $xml)
	{
		foreach($xml->constant as $key => $value)
		{
			$this->set((string)$value->name, (string)$value->value);
		}
	}
}