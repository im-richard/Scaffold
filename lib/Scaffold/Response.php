<?php
/**
 * Scaffold_Response
 *
 * Sets the headers to use for a response. Uses the clients cache and
 * encoding, if it's available, to send the request as best as possible.
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
	 * @var Scaffold_Response_Cache
	 */
	public $cache;
	
	/**
	 * @var Scaffold_Response_Encoder
	 */
	public $encoder;
	
	/**
	 * What will be rendered
	 * @var string
	 */
	public $output;
	
	/**
	 * Output encoding
	 * @var mixed
	 */
	public $encoding;
	
	/**
	 * @var array
	 */
	public $options;
	
	/**
	 * @var array
	 */
	public $headers = array
	(
		'Content-Length'	=> false,
		'Content-Encoding' 	=> false,
		'Vary'				=> false,
		'Expires'			=> false,
		'ETag'				=> false,
		'Last-Modified'		=> false,
		'Cache-Control'		=> false,
	);
	
	/**
	 * @var array
	 */
	protected $_default_options = array
	(
		/**
		 * "public"  - allow proxies to cache the content.
		 * "private" - only allow browser caching.
		 */
		'scope' => 'public',
		
		/**
		 * Generate an ETag of the output
		 */
		'set_etag' => false,
		
		/**
		 * Calculate the content-length
		 */
		'set_content_length' => true
	);

	/**
	 * @access public
	 * @param $encoder Scaffold_Response_Encoder
	 * @param $cache Scaffold_Response_Cache
	 * @param $options
	 * @return void
	 */
	public function __construct(Scaffold_Response_Encoder $encoder, Scaffold_Response_Cache $cache, $options = array())
	{
		$this->encoder 	= $encoder;
		$this->cache 	= $cache;

		// Get the default options
		$this->options = array_merge($this->_default_options,$options);
		
		// Are we encoding the output?
		$this->encoding = $this->encoder->get_encoding_type();
	}
	
	/**
	 * Renders content to the browsers.
	 * If the clients cache isn't modified, it shouldn't send anything
	 * @access public
	 * @param $content
	 * @param $compress
	 * @return void
	 */
	public function render($use_cache = true)
	{
		// Is the client's browser cache valid?
		$cache = $this->cache->valid(
						$this->headers['Last-Modified'],
						$this->headers['ETag']
					);

		// Cache is still fresh
		if($cache === true AND $use_cache === true)
		{
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
		
		// Send it all to the browser
		$this->send_headers();
		echo $this->output;
		exit;
	}
	
	/**
	 * Sets the output for the response
	 * @access public
	 * @param $content
	 * @param $last_modified
	 * @param $type
	 * @return array
	 */
	public function set($content,$last_modified,$type = 'text/plain')
	{
		$this->output 						= $content;
		$this->headers['Content-Type'] 		= $type;
		$this->headers['Expires'] 			= $this->_time(time() + 31536000);
		$this->headers['Last-Modified'] 	= $this->_time($last_modified);
		$this->headers['Cache-Control'] 	= $this->options['scope'];

		# If we're manually encoding the content
		if($this->encoding !== false)
		{
			$this->output = $this->encoder->encode($content);
			$this->headers['Content-Encoding'] = $this->encoding;
			$this->headers['Vary'] = 'Accept-Encoding';
		}
		
		# If you're letting Apache doe the encoding, it will calculate this and override it
		# Sending Content-Length in CGI can result in unexpected behavior
		if($this->options['set_content_length'] === true AND stripos(PHP_SAPI, 'cgi') !== true)
		{
			$this->headers['Content-Length'] = strlen($content);
		}
		
		# You may want to set an etag
		if($this->options['set_etag'] === true)
		{
			$this->headers['ETag'] = $this->generate_etag(
												$last_modified,
												$this->headers['Content-Length'],
												$this->encoding
											);
		}
	}
	
	/**
	 * Generates an etag from an array of values
	 * @access public
	 * @param $array
	 * @return string
	 */
	public function generate_etag($last_modified,$length,$encoding = false)
	{
		return '"' . md5(serialize(array($last_modified,$length,$encoding))) . '"';
	}

	/**
	 * Sends all of the stored headers to the browser
	 * @access public
	 * @return void
	 */
	public function send_headers($status = 200)
	{
		if(!headers_sent())
		{
			foreach($this->headers as $name => $value)
			{
				if($value === false)
					continue;
	
				if(is_string($name))
					$value = "{$name}: {$value}";

				header($value,TRUE,$status);
			}
		}
	}

	/**
	 * Valid time for headers
	 * @access public
	 * @param $time
	 * @return string
	 */
	private function _time($time)
	{
		 return gmdate('D, d M Y H:i:s \G\M\T', $time);
	}
}