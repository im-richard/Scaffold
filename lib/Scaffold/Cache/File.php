<?php

/**
 * Scaffold_Cache_File
 *
 * Uses the file system for caching using ID-based file names
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Cache_File extends Scaffold_Cache
{
	/**
	 * The server path to the cache directory
	 * @var string
	 */
	public $directory;
	
	/**
	 * The maximum age of cache files
	 * @var mixed
	 */
	public $max_age;

	// =========================================
	// = Constructors & Initialization Methods =
	// =========================================

	/**
	 * Constructor
	 *
	 * @param 	$path 	The path to the cache directory
	 * @return 	void
	 */
	public function __construct($path,$max_age)
	{
		$this->directory 	= realpath($path) . DIRECTORY_SEPARATOR;
		$this->max_age 		= $max_age;
	}

	// =========================================
	// = Get and set cache methods =
	// =========================================
	
	/**
	 * Retrieve a value based on an id
	 *
	 * @param $id 
	 * @param $relative_time Check the cache relative to this time. Defaults to time()
	 * @param $default [Optional] Default value to return if id not found
	 * @return mixed
	 * @access public
	 */
	public function get($id, $relative_time = false, $default = false)
	{
		$time = ($relative_time !== false) ? $relative_time : time();
	
		if($file = $this->find($id))
		{
			$data = file_get_contents($file);
			$data = json_decode($data);
			
			// If we're past the expiry date
			if($time >= $data->expires)
				return $default;
				
			return $data;
		}

		return $default;
	}

	/**
	 * Set a value based on an id.
	 *
	 * @param string $id 
	 * @param string $data 
	 * @param integer $last_modified When the source file was last modified
	 * @return boolean
	 * @access public
	 */
	public function set($id,$data,$last_modified = null)
	{	
		$target = $this->directory.$id;
		
		if(!is_dir(dirname($target)))
		{
			$this->create(dirname($id));
		}
		
		# Serialize the data
		$data = json_encode((object) array(
			'contents'  	=> (is_array($data)) ? serialize($data) : $data,
			'last_modified'	=> (isset($last_modified)) ? $last_modified : time(),
			'expires' 		=> time() + $this->max_age,
		));

		return file_put_contents($target,$data);
	}

	// =========================================
	// = Delete Methods =
	// =========================================

	/**
	 * Removes a cache item
	 * @param $id
	 * @return boolean
	 */
	public function delete($id)
	{
		if($this->exists($id))
		{
			@unlink($this->find($id));
			return true;
		}
		return false;
	}
	
	/**
	 * Clear out the cache directory
	 * @param 	$dir
	 * @access	public
	 * @return 	boolean
	 */
	public function empty_dir($dir = false)
	{
		// Start from the cache directory
		if($dir === false)
		{
			$dir = $this->directory;
		}
		else
		{
			if($this->exists($dir) === false)
			{
				return false;
			}
			
			$dir = $this->find($dir);
		}
		
		// Loop through each of the files and directories
		foreach(glob($dir.'/*',GLOB_MARK) as $file)
		{ 
			if(is_dir($file))
			{ 
				$this->empty_dir($file);
				@rmdir($file); 
			}
			else 
			{
				@unlink($file); 
			}
		}
		
		return true;
	}
	
	/**
	 * Deletes a directory. Empties the directory then deletes it.
	 * @access public
	 * @param string $dir 
	 * @return void
	 */
	public function delete_dir($id)
	{
		$this->empty_dir($id);
		@rmdir($this->find($id));
		return true;
	}
	
	/**
	 * Clears out everything within the cache folder
	 * @return void
	 * @access public
	 */
	public function delete_all()
	{
		$this->empty_dir();
	}

	// =========================================
	// = Cache Item Methods =
	// =========================================
	
	/**
	 * Returns the full path of the cache file, if it exists
	 * @param $id
	 * @return boolean
	 */
	public function exists($id)
	{
		$file = $this->directory.$id;
		return (is_file($file) OR is_dir($file)); 
	}
	
	/**
	 * Returns the last modified date of a cache file
	 *
	 * @param $file
	 * @return int
	 */
	public function expires($id)
	{
		return ($this->exists($id)) ? $this->load($id)->expires : false;
	}
	
	/**
	 * Finds a file or directory inside the cache and returns it's full path
	 * @access public
	 * @param $file
	 * @return string
	 */
	public function find($id)
	{
		$file = $this->directory.$id;
		return (file_exists($file)) ? $file : false;
	}

	/**
	 * Create the cache file directory
	 * @access public
	 * @param $path
	 * @return void
	 */
	public function create($path)
	{
		// Create the directories inside the cache folder
		$next = "";
				
		foreach(explode('/',$path) as $dir)
		{
			$next = '/' . $next . '/' . $dir;

			if(!is_dir($this->directory.$next)) 
			{
				mkdir($this->directory.$next);
				chmod($this->directory.$next, 0777);
			}
		}
	}
}