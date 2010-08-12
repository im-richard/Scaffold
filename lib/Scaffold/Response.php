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
	public $output = false;
	
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
	 * @var int
	 */
	public $status = 200;
	
	/**
	 * @var array
	 */
	public $headers = array
	(
		'Content-Encoding' 	=> false,
		'Vary'				=> false,
		'Expires'			=> false,
		'ETag'				=> false,
		'Last-Modified'		=> false,
		'Cache-Control'		=> false,
	);
	
	/**
	 * HTTP status codes and messages
	 * @var array
	 */
	public $messages = array(
		200 => 'OK',
		304 => 'Not Modified',
	);
	
	/**
	 * @var array
	 */
	protected $_default_options = array
	(
		'cache_control' => 'max-age=31536000,must-revalidate,public',
		'set_etag' 		=> true,
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
		// This will handle encoding the output
		$this->encoder = $encoder;
		
		// Lets us check the browsers cache
		$this->cache = $cache;

		// Get the default options
		$this->options = array_merge($this->_default_options,$options);
		
		// Are we encoding the output?
		$this->encoding = $this->encoder->get_encoding_type();
		
		// If we're encoding the output we need to set some headers
		if($this->encoding !== false)
		{
			$this->headers['Content-Encoding'] 	= $this->encoding;
			$this->headers['Vary'] 				= 'Accept-Encoding';
		}
	}
	
	/**
	 * Renders content to the browsers.
	 * If the clients cache isn't modified, it shouldn't send anything
	 * @access public
	 * @param $use_cache boolean Use the browsers cache to determine if we should send the content
	 * @return void
	 */
	public function render($use_cache = true)
	{
		// Is the browser cache still valid?
		$cache_valid = $this->cache->valid($this->headers['Last-Modified'],$this->headers['ETag']);
		
		if($cache_valid AND $use_cache)
		{
			$this->status = 304;
			$this->headers = array();
			$this->send_headers();
			exit;
		}
		
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
		$this->headers['Cache-Control'] 	= $this->options['cache_control'];

		if($this->encoding !== false)
		{
			$this->output = $this->encoder->encode($content);
		}

		if($this->options['set_etag'] === true)
		{
			$this->headers['ETag'] = $this->generate_etag($this->output);
		}
	}
	
	/**
	 * Generates an etag from an array of values
	 * @access public
	 * @param $array
	 * @return string
	 */
	public function generate_etag()
	{
		$args = func_get_args();
		return '"' . hash('md5',serialize($args)) . '"';
	}

	/**
	 * Sends all of the stored headers to the browser
	 * @access public
	 * @return void
	 */
	public function send_headers()
	{
		if(!headers_sent())
		{
			if (isset($_SERVER['SERVER_PROTOCOL']))
			{
				// Use the default server protocol
				$protocol = $_SERVER['SERVER_PROTOCOL'];
			}
			else
			{
				// Default to using newer protocol
				$protocol = 'HTTP/1.1';
			}
			
			// HTTP status line
			header($protocol.' '.$this->status.' '.$this->messages[$this->status]);

			foreach($this->headers as $name => $value)
			{
				if($value === false)
					continue;
	
				if(is_string($name))
					$value = "{$name}: {$value}";

				header($value,true,$this->status);
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
