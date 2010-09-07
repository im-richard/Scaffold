<?php

/**
 * Scaffold_Extension_Time
 *
 * Adds various time-based functionality to Scaffold.
 * - Adds time variables if the Variables extension is enabled
 *
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 * @link			http://oocss.org/spec/css-variables.html
 */
class Scaffold_Extension_Time extends Scaffold_Extension
{
	protected $types = array
	(
		'hour' 			=> 'G',
		'date' 			=> 'j',
		'day' 			=> 'l',
		'day_of_year' 	=> 'z',
		'week' 			=> 'W',
		'month'			=> 'F',
		'year' 			=> 'Y'
	);
	
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array(
		'offset' => 0
	);
	
	/**
	 * The current times
	 */
	public $current = array();
	
	public function __construct($config = array())
	{
		// Run the standard constructors
		parent::__construct($config);
		
		// Set the current time in the local array
		$now = time() + (60 * 60 * $this->config['offset']);
		$this->set_current_time($now);
	}
	
	/**
	 * Create variables for the current time
	 * @access public
	 * @param $source
	 * @param $scaffold
	 * @return string
	 */
	public function variables_replace($source,$variables)
	{
		$time = array();
		
		// Add each of the basic units
		foreach(array_keys($this->types) as $unit)
		{
			$time[$unit] = $this->current[$unit];
		}
		
		// Last modified time of the file
		$time['now'] = gmdate('D, d M Y H:i:s', time());
		
		// Create the var array
		$vars = array('time'=>$time);
		
		// And merge it 
		$variables->variables = array_merge($variables->variables,$vars);
	}
	
	/**
	 * Sets flags for different times of the day
	 * @return void
	 */
	public function flag()
	{
		$condition_types = array_keys($this->types);
		
		foreach(Scaffold::$config['Time']['flags'] as $flag_name => $conditions)
		{
			foreach($condition_types as $check)
			{
				if(!isset($conditions[$check]))
					continue;
				
				if(is_array($conditions[$check]))
				{
					$result[] = self::is_between($conditions[$check]['from'],$conditions[$check]['to'],$check,$now);
				}
				else
				{
					$result[] = ($conditions[$check] == self::$current[$check]) ? true : false;
				}
			}

			# It met all the conditions! Set the flag me hearties!
			if( array_search(false, $result) === false )
			{
				Flags::set($flag_name);
			}
			
			# Reset the results array
			$result = array();
		}
	}
	
	/**
	 * Sets the current date and time
	 *
	 * @author Anthony Short
	 * @return return type
	 */
	private function set_current_time($now)
	{		
		foreach($this->types as $type => $id)
		{
			$this->current[$type] = date($id,$now);		
		}
	}
	
	/**
	 * Checks to see if it's between the two numbers
	 * @param $from
	 * @param $to
	 * @param $type
	 * @param $now
	 * @return boolean
	 */
	private function is_between($from,$to,$type,$now)
	{
		$check = date($this->types[$type],$now);
		return ($check >= $from && $check <= $to);
	}	
	
}