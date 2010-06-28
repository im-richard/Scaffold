<?php
/**
 * Scaffold_Extension_ServerImport
 *
 * This allows you to include files before processing for compiling
 * into a single file and later cached. 
 * 
 * Usage: @server import 'path/to/file.css';
 *
 * @package 		Scaffold
 * @subpackage		Engine
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_ServerImport extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array();
	
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
	public function initialize()
	{
		if($this->scaffold->data['source']->type != 'file')
			return;

		// The source contents as per usual
		$css = $this->scaffold->data['source']->get();
		$path = $this->scaffold->data['source']->path;
		
		// Replace all the imports
		$this->scaffold->data['source']->set($this->find_and_replace($css,$path));
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
		preg_match_all('/\@server\s+import\s+(?:\'|\")([^\'\"]+)(?:\'|\")\;/', $css, $matches);
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
			$line = Scaffold_Helper_CSS::line_number($rules[0][0],$css);
			
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