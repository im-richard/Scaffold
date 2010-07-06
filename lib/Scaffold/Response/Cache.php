<?php

/**
 * Scaffold_Response_Cache
 *
 * Makes checking the requesting user agents cache easier. It extracts the
 * etag and last-modified headers and allows you to compare another etag
 * and modified time against these to determine if the cache is still valid.
 *
 * It automatically fixes the HTTP_IF_MODIFIED_SINCE header issue in IE6.
 * 
 * @package 		Scaffold
 * @subpackage		Response
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
 
class Scaffold_Response_Cache 
{
	/**
	 * The modified-since header from the browser
	 *
	 * @var int
	 */
	private $_modified_since = false;
	
	/**
	 * The clients etag list
	 *
	 * @var string
	 */
	private $_etag = false;

	/**
	 * Constructor
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct()
	{
		// Get the modified since header and store it
		$this->_modified_since = $this->_get_modified_since_header();
		
		// The client etags
		$this->_etag = $this->_get_if_none_match();
	}
	
	// ============================
	// = Public Methods =
	// ============================
	
	/**
	 * Checks if the users cache is still valid by checking the last modified
	 * time and etag against the browsers sent header.
	 *
	 * @access public
	 * @param $last_modified
	 * @param $etag
	 * @return boolean
	 */
	public function valid($last_modified,$etag)
	{
		return ($this->_is_modified($last_modified) === false AND $this->_matched_etag($etag) === true);
	}
	
	/**
	 * Gets the client etags
	 *
	 * @access public
	 * @return string
	 */
	public function get_etag()
	{
		return $this->_etag;
	}
	
	/**
	 * Gets the modified-since header
	 *
	 * @access public
	 * @param $param
	 * @return int
	 */
	public function get_modified_since()
	{
		return $this->_modified_since;
	}
	
	// ============================
	// = Constructor Methods =
	// ============================
	
	/**
	 * Gets the HTTP_IF_MODIFIED_SINCE header
	 *
	 * @access private
	 * @return int
	 */
	private function _get_modified_since_header()
	{
		// No modified header, it's obviously not valid
		if(!isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			return 0;
		}

		$header = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

		// IE6 and perhaps other IE versions send length too, compensate here
		if(($strpos = strpos($header, ';')) !== false)
		{
			$header = substr($header, 0, $strpos);
		}

		return strtotime($header);
	}
	
	/**
	 * Gets the etag (if-none-match) header if available
	 *
	 * @access private
	 * @return string
	 */
	private function _get_if_none_match()
	{
		if(isset($_SERVER['HTTP_IF_NONE_MATCH']))
		{
		   return str_replace('"','',$_SERVER['HTTP_IF_NONE_MATCH']);
		}
		
		return false;
	}
	
	// ============================
	// = Cache Checking Methods =
	// ============================

	/**
	 * ETags match
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	private function _matched_etag($etag)
	{
		return ($etag == $this->_etag);
	}
	
	/**
	 * Has the resource been modified?
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	private function _is_modified($last_modified)
	{
		return ($last_modified > $this->_modified_since);
	}
}