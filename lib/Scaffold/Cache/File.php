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
		$this->current_time	= time();
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
		$time = ($relative_time !== false) ? $relative_time : $this->current_time;
		$file = $this->find($id);
		
		if(file_exists($file))
		{
			$data = file_get_contents($file);
			$data = json_decode($data);
			
			// If the file CAN expire, and it already has, return nothing
			if($data->expires !== false AND $time >= $data->expires)
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
	 * @return string The full path of the file that was just used as a cache
	 * @access public
	 */
	public function set($id,$data,$last_modified = false,$expires = true,$encode = true)
	{	
		$target = $this->directory.$id;
		
		if(!is_dir(dirname($target)))
		{
			$this->create(dirname($id));
		}
		
		// When to set the cache mod time
		$last_modified = ($last_modified === false) ? $last_modified : $this->current_time;
		
		if($encode === true)
		{
			// If max age or expires is false, the cache will never expire
			$expires = ($expires === false OR $this->max_age === false) ? false : $this->current_time + $this->max_age;
			
			# Serialize the data
			$data = json_encode((object) array(
				'contents'  	=> (is_array($data)) ? serialize($data) : $data,
				'last_modified'	=> $last_modified,
				'expires' 		=> $expires,
			));
		}
		
		file_put_contents($target,$data);
		touch($target,$last_modified);
		return $target;
	}

	/**
	 * Delete a cache entry based on id, or delete an entire directory
	 * @param string $id 
	 * @return boolean
	 * @access public
	 */
	public function delete($id)
	{
		$item = $this->find($id);
		
		// It's a cache item
		if(is_file($item))
		{
			unlink($item);
			return true;
		}
		
		// It's a directory
		elseif(is_dir($item))
		{
			// Loop through each of the files and directories
			foreach(glob($item.'/*',GLOB_MARK) as $file)
			{ 
				if(is_dir($file))
				{
					$relative_dir = str_replace($item,'/',$file);
					$this->delete($relative_dir);
					rmdir($file); 
				}
				else 
				{
					unlink($file); 
				}
			}
			
			return true;
		}

		return false;
	}
	
	/**
	 * Delete all cache entries
	 * @return boolean
	 * @access public
	 */
	public function delete_all()
	{
		$this->delete('/');
	}	
	
	// =========================================
	// = Local Methods =
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
	 * Gets the full file path for a cache item
	 * @access public
	 * @param $id
	 * @return string
	 */
	public function find($id)
	{
		return $this->directory.$id;
	}

	/**
	 * Create the cache file directory
	 * @access public
	 * @param $path
	 * @return void
	 */
	public function create($path)
	{
		if(is_dir($path))
			return;
		
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