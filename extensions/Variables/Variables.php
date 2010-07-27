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
 * @see				http://oocss.org/spec/css-variables.html
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
		// HOOK //
		$scaffold->notify('variables_start',array($source,$this));
		
		# Save any variables created in the options
		$this->variables = array_merge($this->config['variables'],$this->variables);
		
		// Create a real regex string
		$regex = $scaffold->helper->css->create_regex($this->regex);
	
		// Extract the variables from the source and save them
		if(preg_match_all('/'.$regex.'/xs',$source->contents,$matches))
		{
			foreach ($matches[0] as $key => $value)
			{
				// Default var group
				$group 	= ($matches[1][$key] == false) ? 'var' : trim($matches[1][$key]);
				$values = $scaffold->helper->css->ruleset_to_array($matches[2][$key]);
				
				// If the group exists
				if(isset($this->variables[$group]))
				{
					$values = array_merge($this->variables[$group],$values);
				}

				$this->variables[$group] = $values;
				
				// Remove the at-rule
				$source->contents = str_replace($value,'',$source->contents);
			}
		}
		
		// Replace the variables
		$source->contents = trim($this->replace_variables($source->contents));
		
		// HOOK //
		$scaffold->notify('variables_end',array($source,$this));
	}
	
	/**
	 * Replaces variables in a string
	 * @param string
	 * @return string
	 */
	public function replace_variables($str)
	{		
		// Now replace each of the variables in the CSS string
		foreach($this->variables as $group => $variables)
		{
			// Sort the variables so they replace correctly
			$variables = $this->_sort_array_by_key_length($variables);
			
			foreach($variables as $key => $value)
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
		array_flip($array);
		uasort($array,array($this,'_sort'));
		array_flip($array);	
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