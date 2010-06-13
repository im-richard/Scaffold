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
class Scaffold_Load
{
	/**
	 * Include paths. Used for searching for relative files.
	 *
	 * @var array
	 */
	public $_load_paths = array();
	
	/**
	 * Valid stylesheet file types
	 *
	 * @var array
	 */
	protected $_valid_stylesheets = array('scss','css');
	
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
	 * Parse the CSS file. This takes an array of files, options and configs
	 * and parses the CSS, outputing the processed CSS string.
	 *
	 * @access 	public
	 * @param 	string	Path to the file to parse
	 * @return	string	The processed css file as a string
	 */
	public function file($input,$options = array())
	{
		// Find the file on the filesystem
		$file = $this->_find_file($input);

		// Make sure it's a valid file
		if($this->_valid_file($file) === false)
		{
			throw new Exception("Scaffold cannot parse the requested file - $file");
		}
		
		// The source type
		$file = new Scaffold_Source_File($file,$options);
		
		// Return the object that can be used for compiling and rendering
		return new Scaffold_Source($file);
	}
	
	/**
	 * Compiles a string of CSS
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function string($input,$options = array())
	{
		// The source type
		$string = new Scaffold_Source_String($input,$options);
		
		// Return the object that can be used for compiling and rendering
		return new Scaffold_Source($string);
	}
	
	// ============================
	// = Private Methods =
	// ============================
	
	/**
	 * Makes sure a file is a valid Scaffold file
	 *
	 * @access private
	 * @param $file string
	 * @param $error boolean Throw an error
	 * @throws Exception
	 * @return boolean
	 */
	private function _valid_file($file)
	{
		return in_array(pathinfo($file,PATHINFO_EXTENSION), $this->_valid_filetypes);
	}
	
	/**
	 * Finds a file on the filesystem using the load paths
	 * and the document root. 		
	 *
	 * @access private
	 * @param $file
	 * @return string
	 */
	private function _find_file($file)
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
			if(is_file($real)) return $real;
			
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
		
		// We can't find the file.
		if(!is_file($file))
			throw new Exception('The file cannot be found ['.$file.']');

		return $file;
	}
}