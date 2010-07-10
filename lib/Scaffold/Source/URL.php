<?php
/**
 * Scaffold_Source_Url
 *
 * A source file for Scaffold that pulls the contents from a URL
 * 
 * @package 		Scaffold
 * @subpackage		Source
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Source_Url extends Scaffold_Source
{
	/**
	 * The type of source
	 * @var string
	 */
	public $type = 'url';

	/**
	 * The url to the source file
	 * @var string
	 */
	public $url;
	
	/**
	 * @var string
	 */
	public $contents;
	
	/**
	 * @var string
	 */
	public $original;

	/** 
	 * Constructor
	 */
	public function __construct($url,$options = array())
	{
		$this->options = $options;
		$this->url = $url;
		$this->last_modified = (isset($options['last_modified'])) ? $options['last_modified'] : 0;
		$this->id = (isset($options['id'])) ? $options['id'] : md5($url);
	}
	
	/**
	 * Load the contents of the URL and set the values
	 * @access public
	 * @param $url
	 * @return void
	 */
	private function _load_contents($url)
	{
		if(isset($this->contents))
			return;
	
		$contents = $this->_load_file_from_url($url);
		$this->contents = $this->original = $contents['result'];
		$this->last_modified = $contents['mod_time'];
	}
	
	/**
	 * Loads a file from a Url using CURL
	 * @access public
	 * @param $url
	 * @return string
	 */
	private function _load_file_from_url($url)
	{
		$result = false;
		$curl = curl_init($url);
		
		if (is_resource($curl) === true)
		{
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FILETIME, true);
			
			$result = curl_exec($curl);
			
			// Get the modified time of the file
			$mod_time = curl_getinfo($curl, CURLINFO_FILETIME);
			
			if($mod_time < 0)
				$mod_time = 0;

			// There was an error
			if($result === false)
			{
				// Check to see if there was a curl error
				$error = curl_error($curl);
				
				// There is a curl error
				if($error != false)
				{
					throw new Exception($error);
				}
			}
			
			curl_close($curl);
		}

		return array(
			'result' => $result,
			'mod_time' => $mod_time
		);
	}
	
	/**
	 * Return the URL of the source
	 * @access public
	 * @return string
	 */
	public function url()
	{
		return $this->url;
	}
	
	/**
	 * Returns the contents of the original source file
	 * @access public
	 * @return string
	 */
	public function original()
	{			
		$this->_load_contents($this->url);
		return $this->original;
	}
	
	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	public function get()
	{
		$this->_load_contents($this->url);
		return $this->contents;
	}
	
	/**
	 * Set the current contents of the source
	 * @access public
	 * @return string
	 */
	public function set($value)
	{
		return $this->contents = $value;
	}
	
	/**
	 * Return the unique id for this source
	 * @access public
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}
	
	/**
	 * Get the last-modified time for this source
	 * @access public
	 * @return string
	 */
	public function last_modified()
	{
		return $this->last_modified;
	}
	
	/**
	 * Finds a file relative to the source file from a URL
	 * @access public
	 * @param $url
	 * @return boolean
	 */
	public function find($url)
	{
		return false;
	}
}