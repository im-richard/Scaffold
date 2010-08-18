<?php

/**
 * Time
 *
 * Sets different flags for different times and dates.
 * 
 * @author Anthony Short
 */
class Time extends Scaffold_Extension
{
	protected static $types = array
	(
		'hour' => 'G',
		'date' => 'j',
		'day' => 'l',
		'day_of_year' => 'z',
		'week' => 'W',
		'month' => 'F',
		'year' => 'Y'
	);
	
	/**
	 * The current times
	 */
	public static $current = array();
	
	/**
	 * Sets flags for different times of the day
	 *
	 * @author Anthony Short
	 * @return void
	 */
	public static function flag()
	{
		$now = time() + (60 * 60 * Scaffold::$config['Time']['offset']);
		
		self::set_current_time($now);

		$condition_types = array_keys(self::$types);
		
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
	private static function set_current_time($now)
	{		
		foreach(self::$types as $type => $id)
		{
			self::$current[$type] = date($id,$now);		
		}
	}
	
	/**
	 * Checks to see if it's between the two numbers
	 *
	 * @author Anthony Short
	 * @param $from
	 * @param $to
	 * @return boolean
	 */
	private static function is_between($from,$to,$type,$now)
	{
		$check = date(self::$types[$type],$now);
		
		if($check >= $from && $check <= $to)
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
	
}