<?php
/**
 * Scaffold_Cache
 *
 * Handles the file caching
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
abstract class Scaffold_Cache
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
	// = Abstract Methods =
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
	abstract public function get($id, $default = NULL);

	/**
	 * Set a value based on an id. Optionally add tags.
	 * 
	 * Note : Some caching engines do not support
	 * tagging
	 *
	 * @param string $id 
	 * @param string $data 
	 * @param integer $lifetime [Optional]
	 * @return boolean
	 * @access public
	 * @abstract
	 */
	abstract public function set($id, $data, $lifetime = NULL);

	/**
	 * Delete a cache entry based on id
	 *
	 * @param string $id 
	 * @param integer $timeout [Optional]
	 * @return boolean
	 * @access public
	 * @abstract
	 */
	abstract public function delete($id);

	/**
	 * Delete all cache entries
	 *
	 * @return boolean
	 * @access public
	 * @abstract
	 */
	abstract public function delete_all();
	
	// =========================================
	// = Protected Methods =
	// =========================================

	/**
	 * Replaces troublesome characters with underscores.
	 *
	 * @param string $id
	 * @return string
	 * @access protected
	 */
	protected function sanitize_id($id)
	{
		// Change slashes and spaces to underscores
		return str_replace(array('/', '\\', ' '), '_', $id);
	}
}