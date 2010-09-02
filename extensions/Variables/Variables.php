<?php
/**
 * Variables
 *
 * Allows you to use variables within your css by defining them within a @variables directive.
 * This is based on the spec developed by Nicole Sullivan.
 *
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 * @link			http://oocss.org/spec/css-variables.html
 */
class Scaffold_Extension_Variables extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array(
		'variables' => array()
	);

	/**
	 * @var array
	 */
	public $variables = array();
	
	/**
	 * @var string
	 */
	private $regex = '@variables (IDENTIFIER)?\s*BLOCK';
	
	/**
	 * Scaffold's process hook
	 * @access public
	 * @param Scaffold
	 * @return void
	 */
	public function process($source,$scaffold)
	{
		// Get variables from the config
		$this->variables = array_merge($this->config['variables'],$this->variables);
	
		// Extract the variables from the source
		$this->variables = array_merge($this->variables,$this->extract($source));
		
		// HOOK //
		$scaffold->notify('variables_start',array($source,$this));

		// Replace the variables
		$source->contents = $this->replace_variables($source->contents,$this->variables);
	}
	
	/**
	 * Returns the 
	
	/**
	 * Finds variable groups in a CSS string
	 * @access public
	 * @param $str
	 * @return str The string with the @variables removed
	 */
	public function extract(Scaffold_Source $source)
	{
		$variables = array();

		// Create a real regex string
		$regex = $this->helper->css->create_regex($this->regex);
		
		if(preg_match_all('/'.$regex.'/xs',$source->contents,$matches))
		{
			foreach ($matches[0] as $key => $value)
			{
				// Default var group
				$group 	= ($matches[1][$key] == false) ? 'var' : trim($matches[1][$key]);
				$values = $this->helper->css->ruleset_to_array($matches[2][$key]);
				
				// If the group exists
				if(isset($variables[$group]))
				{
					// Merge the new variables over the top of them
					$values = array_merge($variables[$group],$values);
				}
				
				// Remove the @variable groups
				$source->contents = str_replace($value,'',$source->contents);
				
				// Save the variable
				$variables[$group] = $values;
			}
		}
		
		return $variables;
	}
	
	/**
	 * Replaces variables in a string
	 * @param $str string
	 * @param $variables array
	 * @return string
	 */
	public function replace_variables($str,array $variables)
	{		
		// Now replace each of the variables in the CSS string
		foreach($variables as $group => $vars)
		{			
			// Sort the variables so they replace correctly
			$sorted = $this->_sort_array_by_key_length($vars);

			foreach($sorted as $key => $value)
			{
				$str = str_replace($group.'.'.$key,$value,$str);
			}
		}
		
		return $str;
	}

	/**
	 * Sets a variables value
	 * @access public
	 * @param $key mixed
	 * @param $value string
	 * @return void
	 */
	public function set($key,$value,$group = null)
	{
		if($group === null) $group = 'var';
		$this->variables[$group][$key] = $value;
	}
	
	/**
	 * Unsets a constant
	 * @param $key
	 * @return void
	 */
	public function remove($group,$key = null)
	{
		if($key === null)
		{
			unset($this->variables[$group]);
		}
		else
		{
			unset($this->variables[$group][$key]);
		}
	}
	
	/**
	 * Returns the constant value
	 * @access public
	 * @param $key
	 * @return string
	 */
	public function get($group,$key = null)
	{
		if($key === null)
		{
			return $this->variables[$group];
		}
		else
		{
			return $this->variables[$group][$key];
		}
	}
	
	/**
	 * Sorts the variables by length.
	 * @access private
	 * @return void
	 */
	private function _sort_array_by_key_length($array)
	{
		$array = array_flip($array);
		uasort($array,array($this,'_sort'));
		$array = array_flip($array);	
		return $array;
	}
	
	/**
	 * Sorts the variables by length.
	 * @access private
	 * @param $a
	 * @param $b
	 */
	private function _sort($a,$b)
	{
		return strlen($b)-strlen($a);
	}
}