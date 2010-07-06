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
class Scaffold_Extension_Constants extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array(
	
		# Do we want to make all the file paths required?
		# If true, an exception will be thrown on missing files
		'constants' => array()

	);

	/**
	 * @var array
	 */
	public $constants = array();
	
	/**
	 * Scaffold's process hook
	 * @access public
	 * @param Scaffold
	 * @return void
	 */
	function process($source)
	{
		// HOOK //
		$this->scaffold->notify('constants_start');
		
		# Extract the constants from the source and save them
		$css = $source->get();
		$this->extract($css);
		$this->constants = array_merge($this->config['constants'],$this->constants);
		$this->scaffold->data['constants'] =& $this->constants;
		
		// HOOK //
		$this->scaffold->notify('constants_before_remove');
		
		# Remove the @constants rules and save them
		$css = $this->remove_constant_rules($css);
		$source->set($css);
		
		// HOOK //
		$this->scaffold->notify('constants_before_replace');
			
		# Now replace each of the constants in the CSS string
		$css = $this->replace($css);
		$source->set($css);

		// HOOK //
		$this->scaffold->notify('constants_end');
	}
	
	/**
	 * Parses the @constants rules
	 * @access public
	 * @param $css
	 * @return void
	 */
	public function extract($css)
	{
		$constants = Scaffold_Helper_CSS::find_atrule('constants',$css);
		
		foreach($constants as $key => $group)
		{
			foreach(Scaffold_Helper_CSS::ruleset_to_array($constants[$key][2]) as $key => $value)
			{
				# This lets constants equal other constants.
				$value = $this->replace($value);
				$this->set($key,$value);
			}
		}
	}
	
	/**
	 * Removes the @constant rules from a CSS string
	 * @access public
	 * @param $css
	 * @return string
	 */
	public function remove_constant_rules($css)
	{
		$rules = Scaffold_Helper_CSS::find_atrule('constants',$css);
		
		foreach($rules as $rule)
		{
			$css = str_replace($rule[0],'',$css);
		}
		
		return $css;
	}

	/**
	 * Sets a constants value
	 * @access public
	 * @param $key mixed
	 * @param $value string
	 * @return void
	 */
	public function set($key,$value)
	{
		$this->constants[$key] = $value;
	}
	
	/**
	 * Unsets a constant
	 * @param $key
	 * @return void
	 */
	public function remove($key)
	{
		unset($this->constants[$key]);
	}
	
	/**
	 * Returns the constant value
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
	 * @access public
	 * @param $css string
	 * @return string
	 */
	public function replace($css)
	{
		// Sort the constants so they replace correctly
		$this->constants = $this->_sort_array_by_key_length($this->constants);
		
		foreach($this->constants as $key => $value)
		{
			$css = preg_replace('/\$'.$key.'/',$value,$css);
		}
		
		return $css;
	}
	
	/**
	 * Sorts the constants by length.
	 * @access private
	 * @return void
	 */
	private function _sort_array_by_key_length($array)
	{
		array_flip($array);
		uasort($array,array($this,'_sort'));
		array_flip($array);	
		return $array;
	}
	
	/**
	 * Sorts the constants by length.
	 * @access private
	 * @param $a
	 * @param $b
	 */
	private function _sort($a,$b)
	{
		return strlen($b)-strlen($a);
	}
}