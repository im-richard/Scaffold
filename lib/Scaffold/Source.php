<?php

/**
 * Scaffold_Source
 *
 * The base file for source objects. Every source object extends this class
 * 
 * @author your name
 */
class Scaffold_Source
{	
	/**
	 * The source driver object
	 * @var object
	 */
	protected $driver;
	
	/**
	 * @var array
	 */
	public $options = array();
	
	/**
	 * Constructor
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct($driver,$options = array())
	{
		$this->driver = $driver;
		$this->options = $options;
	}
	
	/**
	 * Return the original contents of the source
	 * @access public
	 * @return string
	 */
	public function original()
	{
		return $this->driver->original();
	}
	
	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	public function get()
	{
		return $this->driver->get();
	}
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	public function id()
	{
		return $this->driver->id();
	}
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	public function last_modified()
	{
		return $this->driver->last_modified();
	}
}