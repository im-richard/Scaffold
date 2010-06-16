<?php
/**
 * Scaffold_Load
 *
 * - Loads files
 * - Returns the contents of files
 * - Directory listings
 * - Search for files and directories within defined load paths
 * - Create Scaffold_Source objects
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Loader
{
	/**
	 * Include paths. Used for searching for relative files.
	 *
	 * @var array
	 */
	public $_load_paths = array();
	
	/**
	 * Constructor
	 *
	 * @param $load_paths
	 */
	public function __construct($load_paths)
	{
		$this->_load_paths = $load_paths; 
	}
	
	// ============================
	// = Public Methods =
	// ============================

	/**
	 * Finds a file on the filesystem using the load paths
	 * and the document root. 		
	 *
	 * @access private
	 * @param $file
	 * @throws Scaffold_Loader_Exception
	 * @return string
	 */
	public function find_file($file)
	{
		// Docroot relative file
		if($file[0] == DIRECTORY_SEPARATOR)
		{
			$file = $_SERVER['DOCUMENT_ROOT'] . $file;
		}
		
		// Relative File
		else
		{		
			$real = realpath($file);
			
			// We've already found it
			if(is_file($real))
			{
				$file = $real;
			}
			else
			{
				// Go through each of the include paths to find it
				foreach($this->load_paths as $path)
				{
					$search = $path.DIRECTORY_SEPARATOR.$file;
					
					if(is_file($search))
					{
						$file = $search;
						break;
					}
				}
			}
		}
		
		if(!is_file($file))
		{
			throw new Exception('The file cannot be found ['.$file.']');
		}

		return $file;
	}
}