<?php
/**
 * Scaffold_Extension_Import
 *
 * This allows you to include files before processing for compiling
 * into a single file and later cached. Uses the standard CSS syntax.
 * 
 * Usage: @import 'path/to/file.css';
 *
 * @package 		Scaffold
 * @subpackage		Engine
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Import extends Scaffold_Extension
{
	/**
	 * Stores which files have already been included
	 * @var array
	 */
	public $loaded = array();
	
	/**
	 * Before any processing is done
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function pre_process($source,$scaffold)
	{
		$this->replace_rules($source,$scaffold);
	}
	
	/**
	 * Finds any matching at rules
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function find_rules($css)
	{
		preg_match_all(
		    '/
		        @import\\s+
		        (?:url\\(\\s*)?      # maybe url(
		        [\'"]?               # maybe quote
		        (.*?)                # 1 = URI
		        [\'"]?               # maybe end quote
		        (?:\\s*\\))?         # maybe )
		        ([a-zA-Z,\\s]*)?     # 2 = media list
		        ;                    # end token
		    /x',
		    $css,
		    $matches);

		return (count($matches[0]) > 0) ? $matches : false;
	}

	/**
	 * Imports css via @import statements
	 * @param $source Scaffold_Source
	 * @param $scaffold Scaffold
	 * @return void
	 */
	public function replace_rules($source,Scaffold $scaffold)
	{
		if($rules = $this->find_rules($source->contents))
		{	
			foreach($rules[1] as $key => $file)
			{
				if($file = $source->find($file))
				{
					# Only include files once
					if(in_array($file, $this->loaded))
					{
						continue;
					}
					
					$this->loaded[] = $file;
					
					# Use Scaffold to compile the inner CSS file
					$inner = file_get_contents($file);
					
					# Replace the rule		
					$source->contents = str_replace($rules[0][$key],$inner,$source->contents);
				}
			}
		}
	}
	
	/**
	 * Checks if a file has already been loaded
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function is_loaded($file)
	{
		if(!in_array($file,$this->loaded))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}