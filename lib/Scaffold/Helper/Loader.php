<?php
/**
 * Scaffold_Helper_Loader
 *
 * - Loads files
 * - Returns the contents of files
 * - Directory listings
 * - Search for files and directories within defined load paths
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Helper_Loader
{
	/**
	 * Include paths. Used for searching for files.
	 * @var array
	 */
	public $_load_paths = array();
	
	/**
	 * @param $load_paths
	 */
	public function __construct(array $load_paths)
	{
		$this->_load_paths = $load_paths; 
	}

	/**
	 * Adds a load path
	 * @access public
	 * @param $path
	 * @throws Exception
	 * @return void
	 */
	public function add_load_path($path)
	{
		if(is_dir($path))
		{
			$this->_load_paths[] = $path;
		}
		else
		{
			throw new Exception("Path does not exist [$path]");
		}
	}
	
	/**
	 * Removes a load path
	 * @access public
	 * @param $path
	 * @return void
	 */
	public function remove_load_path($path)
	{
		if($key = array_search($path, $this->_load_paths))
		{
			unset($this->_load_paths[$key]);
		}
	}

	/**
	 * Finds a file on the filesystem using the load paths
	 * and the document root. 		
	 * @access public
	 * @param $file
	 * @param $required boolean
	 * @throws Scaffold_Loader_Exception
	 * @return mixed
	 */
	public function file($file,$required = true)
	{
		$real = realpath($file);
		
		// We've already found it
		if(is_file($real))
		{
			$file = $real;
		}
		
		// Docroot relative file
		elseif($file[0] == '/' OR $file[0] == '\\')
		{
			$file = $_SERVER['DOCUMENT_ROOT'] . $file;
		}
		
		// Look in the load paths
		else
		{
			// Go through each of the include paths to find it
			foreach($this->_load_paths as $path)
			{
				$search = $path.DIRECTORY_SEPARATOR.$file;
				
				if(is_file($search))
				{
					$file = $search;
					break;
				}
			}
		}
		
		if(!is_file($file) AND $required === true)
		{
			throw new Exception('The file cannot be found ['.$file.']');
		}
		elseif(!is_file($file) AND $required === false)
		{
			return false;
		}

		return $file;
	}
}