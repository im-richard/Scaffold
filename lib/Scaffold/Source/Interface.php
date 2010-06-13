<?php

/**
 * Scaffold_CSS_Source
 *
 * A single CSS file or string. 
 * 
 * @author your name
 */
interface Scaffold_Source_Interface
{
	/**
	 * Create the file object
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct($content,$options = array());
	
	/**
	 * Return the original contents of the source
	 *
	 * @access public
	 * @return string
	 */
	public function original();
	
	/**
	 * Get the current contents of the source
	 *
	 * @access public
	 * @return string
	 */
	public function contents();
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	public function id();
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	public function last_modified();
}