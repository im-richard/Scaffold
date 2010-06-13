<?php

/**
 * Scaffold_Response_Compressor
 *
 * Determines the available encoding types of the requesting user agent.
 * It can encode/compress content and return it in a format the user agent
 * will understand.
 *
 * It automatically disables compression for IE6, which has bugs with gzip compression.
 * 
 * @package 		Scaffold
 * @subpackage		Response
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */

class Scaffold_Response_Compressor
{
	/**
	 * The method of compression
	 *
	 * @var mixed
	 */
	private $_encoding_method = false;
	
	/**
	 * Compression level
	 *
	 * @var mixed
	 */
	private $_compression_level = false;
	
	/**
	 * Constructor
	 *
	 * @access public
	 * @param $compression_level mixed
	 * @return void
	 */
	public function __construct($level = false)
	{
		if(is_numeric($level) AND $level > 0)
		{
			$this->_compression_level = max(1, min($level, 9));
		}

		if($this->can_encode() AND $this->_compression_level !== false)
		{
			$this->_encoding_method = $this->_encoding_method();
		}
	}

	/**
	 * Encode the output using gzip compression if available. If no
	 * compression is available, or it is disabled, it will just return
	 * the content as normal.
	 *
	 * @access public
	 * @param $content
	 * @return string
	 */
	public function compress($content)
	{
		# Encoding method
		$method = $this->_encoding_method;

		# Compress the content if we can
		return ($method !== false) ? $method($content, $this->_compression_level) : $content;
	}
	
	/**
	 * Gets the compression level
	 *
	 * @access public
	 * @return mixed
	 */
	public function get_compression_level()
	{
		return $this->_compression_level;
	}
	
	/**
	 * Returns the encoding type
	 *
	 * @access public
	 * @return mixed
	 */
	public function get_encoding_method()
	{
		return $this->_encoding_method;
	}
	
	/**
	 * Returns the type of encoding to use in the Content-Encoding header
	 * based on the method that is being used to compress the content.
	 *
	 * @access public
	 * @return mixed
	 */
	public function get_encoding_type()
	{
		if($this->_encoding_method == 'gzencode')
			return 'gzip';
			
		if($this->_encoding_method == 'gzdeflate')
			return 'deflate';
			
		return false;
	}
	
	/**
	 * Checks to make sure that the user agent will
	 * accept encoding. Also checks to make sure 
	 * that compression level isn't set to false.
	 *
	 * IE6 has some bugs with gzipping, so we'll avoid
	 * sending gzipped content to them.
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function can_encode()
	{
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR $this->_ie6_or_below())
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Determines the type of compression to use.
	 *
	 * @access public
	 * @return mixed
	 */
	private function _encoding_method()
	{
		// Check for gzip
		if(strstr($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false)
			return 'gzencode';
		
		// Check for deflate
		if(strstr($_SERVER['HTTP_ACCEPT_ENCODING'],'deflate') !== false)
			return 'gzdeflate';
		
		return false;
	}
	
	/**
	 * Detects if the UA is IE6 or below. These versions had buggy gzip implementations
	 * even though they sent the correct Accepted Content header.
	 *
	 * Borrowed from Minify.
	 *
	 * @access private
	 * @see http://code.google.com/p/minify/
	 * @return boolean
	 */
	private function _ie6_or_below()
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}

		$ua = $_SERVER['HTTP_USER_AGENT'];

		// quick escape for non-IEs
		if(strpos($ua, 'Mozilla/4.0 (compatible; MSIE ') !== 0)
		{
		    return false;
		}

		// If it's IE6 or lower, we don't want to encode.
		if((float)substr($ua, 30) < 7)
		{
			return true;
		}
	}
}