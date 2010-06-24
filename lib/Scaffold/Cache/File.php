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
	 * @var mixed
	 */
	protected $expires = false;

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
		$this->directory 	= realpath($path) . DIRECTORY_SEPARATOR;
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
			$data = file_get_contents($this->find($id));
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
		$target = $this->find($id);
		
		if(!is_dir(dirname($target)))
		{
			$this->create(dirname($id));
		}
		
		$data = (is_array($data)) ? serialize($data) : $data;

		# Write the data
		file_put_contents($target, $data);
		chmod($target, 0777);
		touch($target, time());
		
		return true;
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
				// @todo Throw error
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
	 * Checks if a cache item has expired. Cache files are expired
	 * if the original file has been modified since the cache file was
	 * created. It can also expire after a certain amount of time.
	 *
	 * @param $id
	 * @param $time
	 * @return boolean
	 */
	public function expired($id,$time)
	{
		$expired = false;
		$modified = $expires = $this->modified($id);
	
		if($this->expires != false)
		{
			$expires += $this->expires;
			$expired = (time() >= $expires);
		}

		return ($expired OR $modified <= $time);
	}
	
	/**
	 * Set the maximum age for cache files
	 * @param string $int 
	 * @return void
	 * @access public
	 */
	public function set_expires($int)
	{
		$this->expires = $int;
	}
	
	/**
	 * Returns the full path of the cache file, if it exists
	 *
	 * @param $file
	 * @return string
	 */
	public function exists($id)
	{
		if(is_file($this->find($id)) OR is_dir($this->find($id)))
		{
			return true;
		}
		else
		{
			return false;
		}
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
	 * Finds a file or directory inside the cache and returns it's full path
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