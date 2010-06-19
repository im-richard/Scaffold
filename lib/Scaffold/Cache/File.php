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
	protected $directory;
	
	/**
	 * Local cache lifetime
	 * @var int
	 */
	protected $expires;

	// =========================================
	// = Constructors & Initialization Methods =
	// =========================================

	/**
	 * Constructor
	 *
	 * @param 	$path 	The path to the cache directory
	 * @return 	void
	 */
	public function __construct($path,$expires)
	{
		$this->directory 	= $path;
		$this->expires 		= $expires;
	}

	// =========================================
	// = Get and set cache methods =
	// =========================================
	
	/**
	 * Retrieve a value based on an id
	 *
	 * @param string $id 
	 * @param string $default [Optional] Default value to return if id not found
	 * @return mixed
	 * @access public
	 * @abstract
	 */
	public function get($id, $default = NULL)
	{
		if($file = $this->exists($id))
		{
			// Get the contents of the file
			$data = file_get_contents($file);
		}

		return isset($data) ? $data : $default;
	}

	/**
	 * Set a value based on an id.
	 *
	 * @param string $id 
	 * @param string $data 
	 * @param integer $lifetime [Optional]
	 * @return boolean
	 * @access public
	 */
	public function set($id,$data,$lifetime = NULL)
	{	
		# Create the cache file
		$target = $this->find($id);
		
		# If the target directory doesn't exist
		if(!is_dir(dirname($target)))
		{
			$this->create(dirname($id));
		}
		
		# What to write
		$data = (is_array($data)) ? serialize($data) : $data;

		# Put it in the cache
		file_put_contents($target, $data);
		
		# Set its parmissions
		chmod($target, 0777);
		touch($target, time());
	}

	// =========================================
	// = Delete Cache Methods =
	// =========================================

	/**
	 * Removes a cache item
	 *
	 * @param $file
	 * @return boolean
	 */
	public function delete($id)
	{
		if($file = $this->find($id))
		{
			@unlink($file);
		}
	}
	
	/**
	 * Clear out the cache directory
	 *
	 * @param 	$id
	 * @access	public
	 * @return 	boolean
	 */
	public function delete_all($dir = false)
	{
		// Start from the cache directory
		if($dir === false)
		{
			$dir = $this->directory;
		}
		
		// Loop through each of the files and directories
		foreach(glob($dir.'*',GLOB_MARK) as $file)
		{ 
			// If it's a directory, clean it out, then remove it
			if(is_dir($file))
			{ 
				$this->delete_all($file);
				@rmdir($file); 
			}
			
			// Otherwise just remove the file
			else 
			{
				@unlink($file); 
			}
		}
	}

	// =========================================
	// = Cache Item Methods =
	// =========================================

	/**
	 * Checks if a cache item has expired.
	 *
	 * @param $id
	 * @param $time
	 * @return boolean
	 */
	public function expired($id,$time)
	{		
		# When the cache item was modified
		$modified = $expires = $this->modified($id);
		
		# When the output file expires
		$expires =+ $this->expires;

		# If the file has expired, or the original is newer
		return (time() >= $expires OR $modified < $time OR $modified === 0);
	}
	
	/**
	 * Returns the full path of the cache file, if it exists
	 *
	 * @param $file
	 * @return string
	 */
	public function exists($id)
	{
		return is_file($this->find($id));
	}
	
	/**
	 * Returns the last modified date of a cache file
	 *
	 * @param $file
	 * @return int
	 */
	public function modified($id)
	{
		return ( $this->exists($id) ) ? filemtime($this->find($id)) : 0;
	}
	
	/**
	 * Finds a file inside the cache and returns it's full path
	 *
	 * @param $file
	 * @return string
	 */
	public function find($id)
	{
		return $this->directory.$id;
	}

	/**
	 * Create the cache file directory
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