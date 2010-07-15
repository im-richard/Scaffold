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
		// Can only parse files, obviously
		if($source->type != 'file') return;

		// Replace all the imports
		$source->contents = $this->find_and_replace($source->contents,$source->path);
	}
	
	/**
	 * Search through a CSS string, and recursively replace import rules
	 *
	 * @author your name
	 * @param $css
	 * @param $base Search for files relative to this file
	 * @return return type
	 */
	public function find_and_replace($css,$base)
	{
		while($rules = $this->find_rules($css))
		{
			$css = $this->replace_rules($rules,$css,$base);
		}
		
		return $css;
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
	 * @param $rules mixed
	 * @param $css string
	 * @param $base Search for files relative to this file
	 * @return string
	 */
	public function replace_rules($rules,$css,$base)
	{
		$unique = array_unique($rules[1]);
		$include = str_replace("\\", "/", $unique[0]);
		
		# If they haven't supplied an extension, we'll assume its a css file
		if(pathinfo($include, PATHINFO_EXTENSION) == "")
			$include .= '.css';
		
		# The base is set, and it's relative
		if($include[0] != DIRECTORY_SEPARATOR)
		{
			$path = dirname($base) . DIRECTORY_SEPARATOR . $include;
		}
		elseif($include[0] == DIRECTORY_SEPARATOR)
		{
			$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $include;
		}
		
		if(!is_file($path))
		{
			// Error message
			$title = "Missing File";
			$message = "<code>$include</code> cannot be found or doesn't exist.<pre><code> ".$rules[0][0]."</code></pre>";
			$line = Scaffold_Helper_String::line_number($rules[0][0],$css);
			
			// Throw the error
			throw new Scaffold_Extension_Exception($title,$message,$base,$line);
		}
		
		# It's already been loaded
		if($this->is_loaded($path))
		{
			$css = str_replace($matches[0][0],'', $css);
		}
			
		$this->loaded[] = $path;
		
		# Check the file for more imports
		$contents = file_get_contents($path);	
		$contents = $this->find_and_replace($contents,$path);

		# Replace the rule		
		$css = str_replace($rules[0][0],$contents,$css);

		return $css;
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