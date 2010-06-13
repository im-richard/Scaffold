<?php

/**
 * Scaffold_CSS_Source
 *
 * A single CSS file or string. 
 * 
 * @author your name
 */
class Scaffold_Source
{
	/**
	 * The source object
	 *
	 * @var Scaffold_Source_Interface
	 */
	private $_source;
	
	/**
	 * Create the source object
	 *
	 * @access public
	 * @param $source Scaffold_Source_Interface
	 * @return void
	 */
	public function __construct(Scaffold_Source_Interface $source)
	{
		$this->_source = $source;
	}
	
	/**
	 * Return the original contents of the source
	 *
	 * @access public
	 * @return string
	 */
	public function original()
	{
		return $this->_source->original();
	}
	
	/**
	 * Get the current contents of the source
	 *
	 * @access public
	 * @return string
	 */
	public function contents()
	{
		return $this->_source->contents();
	}
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	public function id()
	{
		return $this->_source->id();
	}
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	public function last_modified()
	{
		return $this->_source->last_modified();
	}
	
	/**
	 * Finds a file relative to the source file
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function find_file($file)
	{
		$target = $this->_source->directory() . DIRECTORY_SEPARATOR . $file;

		if($this->_source->directory() !== false AND file_exists($target))
		{
			return $target;
		}
		
		return false;
	}
}