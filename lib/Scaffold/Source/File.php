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
class Scaffold_Source_File implements Scaffold_Source_Interface
{
	/**
	 * A unique identifier
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $last_modified;

	/**
	 * @var string
	 */
	public $contents;

	/**
	 * The original content before modification
	 * @var string
	 */
	public $original;

	/** 
	 * Constructor
	 */
	public function __construct($content,$options = array())
	{
		$this->options = $options;
		$this->contents = file_get_contents($content);
		$this->original = $content;
		$this->last_modified = (isset($options['last_modified'])) ? $options['last_modified'] : filemtime($content);
		$this->id = (isset($options['id'])) ? $options['id'] : md5(serialize(array($content,$this->last_modified)));
	}
	
	/**
	 * Return the original contents of the source
	 * @access public
	 * @return string
	 */
	public function original()
	{
		return file_get_contents($this->original);
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
}