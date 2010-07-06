<?php
/**
 * Scaffold_Response
 *
 * - Renders content to the browser
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Response
{
	/**
	 * Conditional Get headers
	 * @var Scaffold_Reponse_Cache
	 */
	private $_cache;
	
	/**
	 * Content encoder
	 * @var Scaffold_Reponse_Encoder
	 */
	private $_encoder;
	
	/**
	 * The headers to be sent to the browser
	 * @var array
	 */
	public $headers = array();
	
	/**
	 * if true, the Cache-Control header will contain 
	 * "public", allowing proxies to cache the content. Otherwise "private" will 
	 * be sent, allowing only browser caching.
	 * @var boolean
	 */
	private $_scope;
	
	/**
	 * The max-age header
	 * @var int
	 */
	private $_max_age;
	
	/**
	 * When the cache expires
	 * @var int
	 */
	private $_expires;
	
	/**
	 * Status
	 * @var int
	 */
	private $_status = 200;

	/**
	 * Last modified header
	 * @var string
	 */
	private $_last_modified = 0;
	
	/**
	 * If given, only the ETag header can be sent with
	 * content (only HTTP1.1 clients can conditionally GET). The string is
	 * an md5 hash of a string and changes whenever the resource changes. 
	 * This is not needed/used if $_last_modified is set.
	 * @var string
	 */
	private $_content_hash = false;
	
	/**
	 * Default Options
	 * @var array
	 */
	protected $_default_options = array
	(
		'scope' 		=> 'public',
		'content_type' 	=> 'text/css',
		'max_age'		=> 3600,
		'far_future_expires_header' => true
	);

	/**
	 * Constructor
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct(Scaffold_Response_Compressor $encoder, Scaffold_Response_Cache $cache, $options = array())
	{
		// Set the dependencies
		$this->_encoder = $encoder;
		$this->_cache = $cache;
		
		// Get the default options
		$options = array_merge($this->_default_options,$options);
		
		// Max age of the cache
		$this->_max_age = $options['max_age'];
		
		// Cache scope
		$this->_scope = $options['scope'];

		// Content type
		$this->_set_content_type($options['content_type']);
		
		// Cache control
		$this->_set_cache_control($this->_max_age,$this->_scope);
		
		// Expires Header
		if($options['far_future_expires_header'] === true)
		{
			$this->_max_age = time() + 30;
		}
		
		// If encoding is enabled
		if($this->_encoder->get_encoding_method() !== false)
		{
			# This header must be sent with compressed content to prevent browser caches from breaking
			$this->_add_header('Vary','Accept-Encoding');

			# Send the content encoding header
			$this->_add_header('Content-Encoding',$this->_encoder->get_encoding_type());
		}
	}
	
	/**
	 * Renders content to the browsers.
	 * If the clients cache isn't modified, it shouldn't send anything
	 * @access public
	 * @param $content
	 * @param $compress
	 * @return void
	 */
	public function render($content,$last_modified,$use_cache = true,$output_type = null)
	{
		// Compress the content if we can
		// This will just return the content if we can't
		$content = $this->_encoder->compress($content);
		
		// Set the content length header
		$this->_set_content_length($content);
		
		// Set the last modified time
		$this->_set_last_modified($last_modified);
		
		// Set expiration time for this file. The last modified time + lifetime
		$this->_set_expires($last_modified + $this->_max_age);

		// Set the etag for this file
		$this->_set_etag($last_modified,$this->_encoder->get_encoding_type(),$this->_content_length);

		// The clients cache is still fresh, so we don't
		// need to display any content to the browser.
		if($this->_valid_cache() === true AND $use_cache == true)
		{
			$this->_not_modified();
		}
		else
		{
			$this->send_headers();
			echo $content;
		}
		
		exit;
	}
	
	/**
	 * Sets a not modified header
	 *
	 * @access private
	 * @return void
	 */
	private function _not_modified()
	{
		header('HTTP/1.1 304 Not Modified');
	}
	
	/**
	 * Checks to see if the client cache is valid
	 *
	 * @author your name
	 * @param $param
	 * @return boolean
	 */
	private function _valid_cache()
	{
		return $this->_cache->valid($this->_last_modified,$this->_etag);
	}
	
	/**
	 * Sets the Content-Type header
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	private function _set_content_type($type)
	{
		$this->_content_type = $type;
		$this->_add_header('Content-Type',$type);
	}
	
	/**
	 * Sets the content length. Sending Content-Length in CGI can result in unexpected behavior
	 *
	 * @access private
	 * @param $length
	 * @return void
	 */
	private function _set_content_length($content)
	{
		if(stripos(PHP_SAPI, 'cgi') === FALSE)
		{
			$this->_content_length = strlen($content);
			$this->_add_header('Content-Length',$this->_content_length);
		}
	}
	
	/**
	 * Sets the etag
	 *
	 * @access private
	 * @param $param
	 * @return void
	 */
	private function _set_etag()
	{
		// Get all the arguments and serialize them to create an etag
		$etag = md5(serialize(func_get_args()));
		$this->_etag = $etag;
		$this->_add_header('ETag',$etag);
	}
	
	/**
	 * Sets the Cache control header
	 *
	 * @access private
	 * @param $max_age
	 * @return void
	 */
	private function _set_cache_control($age,$scope)
	{
		$this->_add_header('Cache-Control','max-age='.$age.', ' . $scope);
	}
	
	/**
	 * Sets the last_modified header
	 *
	 * @author your name
	 * @param $last_modified
	 * @return void
	 */
	private function _set_last_modified($last_modified)
	{
		$this->_last_modified = $last_modified;
		$this->_add_header('Last-Modified', $this->_time($last_modified));
	}

	/**
	 * Sets expires header
	 *
	 * @access private
	 * @return void
	 */
	private function _set_expires($expires)
	{
		$this->_expires = $expires;
		$this->_add_header('Expires', $this->_time($expires));
	}

	// ============================
	// = HTTP Header Methods =
	// ============================
	
	/**
	 * Stores a header to send
	 *
	 * @param $name
	 * @param $value
	 * @return void
	 */
	private function _add_header($name,$value)
	{
		$this->headers[$name] = $value;
	}
	
	/**
	 * Gets the value of a header
	 *
	 * @access private
	 * @param $name
	 * @return void
	 */
	public function header($name)
	{
		return isset($this->headers[$name]) ? $this->headers[$name] : false;
	}
	
	/**
	 * Sends all of the stored headers to the browser
	 *
	 * @access private
	 * @return void
	 */
	public function send_headers()
	{
		if (!headers_sent())
		{
			foreach($this->headers as $name => $value)
			{
				if (is_string($name))
				{
					// Combine the name and value to make a raw header
					$value = "{$name}: {$value}";
				}

				// Send the raw header
				header($value, TRUE, $this->_status);
			}
		}
	}
	
	// ============================
	// = Helper Methods =
	// ============================
	
	/**
	 * Valid time for headers
	 *
	 * @access public
	 * @param $time
	 * @return string
	 */
	private function _time($time)
	{
		 return gmdate('D, d M Y H:i:s \G\M\T', $time);
	}
}