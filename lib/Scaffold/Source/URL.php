<?php
/**
 * Scaffold_Source_Url
 *
 * A source file for Scaffold that pulls the contents from a URL
 * 
 * @package 		Scaffold
 * @subpackage		Source
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Source_Url extends Scaffold_Source
{
	/**
	 * The url to the source file
	 * @var string
	 */
	public $url;

	/** 
	 * Constructor
	 */
	public function __construct($url,$options = array())
	{
		$this->options = $options;
		$this->contents = $this->original = file_get_contents($url);
		$this->url = $url;
		$this->last_modified = (isset($options['last_modified'])) ? $options['last_modified'] : time();
		$this->id = (isset($options['id'])) ? $options['id'] : md5($url);
	}
	
	/**
	 * Return the URL of the source
	 * @access public
	 * @return string
	 */
	public function url()
	{
		return $this->url;
	}
	
	/**
	 * Returns the contents of the original source file
	 * @access public
	 * @return string
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
	 * @access public
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}
	
	/**
	 * Get the last-modified time for this source
	 * @access public
	 * @return string
	 */
	public function last_modified()
	{
		return $this->last_modified;
	}
}