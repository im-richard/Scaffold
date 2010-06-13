<?php

/**
 * Flags
 *
 * Lets you only display chunks of CSS is a particular flag is set. The modules which
 * rely on flagging depend on this module. Without this, they don't do anything.
 *
 * The @flag(name) syntax works as a wrapper for selectors and properties
 *
 *	@flag(ie6)
 *	{
 *		#id
 *		{
 *			zoom:1;
 *		}
 *	}
 *
 * 
 */
class Flags extends Scaffold_Module
{
	/**
	 * Stores the set flags
	 *
	 * @var array
	 */
	public static $flags = array();

	/**
	 * Get the flags from each of the loaded modules
	 * by creating a module hook.
	 *
	 * @return void
	 */
	public static function __construct()
	{
		parent::__construct();

		foreach(Scaffold::modules() as $module)
		{
			$flags = $module->flag();
		}
	}

	/**
	 * Post Process Hook
	 */
	public static function process()
	{
		Scaffold::$css = self::replace(Scaffold::$css);
	}
	
	/**
	 * Sets a cache flag
	 *
	 * @param 	$name	The name of the flag to set
	 * @return 	void
	 */
	public static function set($name)
	{
		return self::$flags[] = $name;
	}
	
	/**
	 * Return all flags, or a single flag
	 *
	 * @param $flag
	 * @return boolean
	 */
	public static function get($flag = false)
	{
		if($flag === false)
		{
			return self::$flags;
		}
		else
		{
			return (in_array($flag,self::$flags)) ? true : false;
		}
	}

	/**
	 * Post Process. Needs to come after the nested selectors.
	 *
	 * @author Anthony Short
	 * @param $css object
	 * @return $css string
	 */
	public static function replace($css)
	{
		if( $found = $css->find_at_group('flag',false) )
		{
			foreach($found['groups'] as $group_key => $group)
			{
				$flags = explode(',',$found['flag'][$group_key]);
				
				# See if any of the flags are set
				foreach($flags as $flag_key => $flag)
				{
					if(Flags::get($flag))
					{
						$parse = true;
						break;
					}
					else
					{
						$parse = false;
					}
				}
				
				if($parse)
				{
					# Just remove the flag name, and it should just work.
					$css->string = str_replace($found['groups'][$group_key],$found['content'][$group_key],$css->string);
				}
				else
				{
					# Get it out of there, that flag isn't set!
					$css->string = str_replace($found['groups'][$group_key],'',$css->string);
				}
			}
			
			# Loop through the newly parsed CSS to look for more flags
			$css = self::replace_flags($css);
		}
		
		return $css;
	}
}