<?php
/**
 * Scaffold_Source_File
 *
 * A source file for Scaffold that is file-based.
 * 
 * @package 		Scaffold
 * @subpackage		Source
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Source_File extends Scaffold_Source
{
	/**
	 * The path to the source file
	 * @var string
	 */
	public $path;
	
	/**
	 * Path used to find relative files
	 * @var string
	 */
	public $basepath;
	
	/**
	 * The type of source
	 * @var string
	 */
	public $type = 'file';

	/** 
	 * Constructor
	 */
	public function __construct($path,$options = array())
	{
		$this->options = $options;
		$this->path = $this->basepath = realpath($path);
		
		if(!is_file($this->path))
		{
			throw new Exception('File does not exist [' . $path . ']');
		}
		
		$this->contents = $this->original = file_get_contents($this->path);
		$this->last_modified = (isset($options['last_modified'])) ? $options['last_modified'] : filemtime($path);
		$this->id = (isset($options['id'])) ? $options['id'] : md5($path);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function path()
	{
		return $this->path;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function basepath()
	{
		return $this->basepath;
	}
	
	/**
	 * Returns the contents of the original source file
	 */
	public function original()
	{
		return $this->original;
	}
	
	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	public function get()
	{
		return $this->contents;
	}
	
	/**
	 * Set the current contents of the source
	 * @access public
	 * @return string
	 */
	public function set($value)
	{
		return $this->contents = $value;
	}
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	public function last_modified()
	{
		return $this->last_modified;
	}
	
	/**
	 * Finds a file relative to the source file from a URL
	 * @access public
	 * @param $url
	 * @return mixed
	 */
	public function find($url)
	{
		if($url[0] == '/' OR $url[0] == '\\')
		{
			$path = $_SERVER['DOCUMENT_ROOT'] . $url;
		}
		else
		{
			$path = dirname($this->basepath) . DIRECTORY_SEPARATOR . $url;
		}

		return (file_exists($path)) ? $path : false;
	}
}