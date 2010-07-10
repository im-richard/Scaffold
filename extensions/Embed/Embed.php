<?php
/**
 * Scaffold_Extension_Embed
 *
 * Uses the Function extension to create a new CSS function called embed().
 * This lets you embed images and other url() data in the CSS as base64 encoded strings.
 * Uses MHTML for IE6/7.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Embed extends Scaffold_Extension
{
	/**
	 * File extensions
	 * @var array
	 */
	private $_extensions = array(
		'images' => array('gif','jpg','jpeg','png'),
		'fonts' => array('otf','ttf','woff')
	);
	
	/**
	 * Registers a custom CSS function.
	 * @access public
	 * @param $functions
	 * @return array
	 */
	public function register_function($functions)
	{
		$functions->register('embed',array($this,'parse'));
	}
	
	/** 
	 * Get the source path
	 * @access public
	 * @param $source
	 */
	public function initialize($source)
	{
		$this->source = $source;
	}
	
	/**
	 * Parses embed() functions within the CSS
	 * @access public
	 * @param $from
	 * @param $to
	 * @return string
	 */
	public function parse($url)
	{
		if($this->source->type == 'file')
		{
			// Path to the source file on the server. Used for relative paths.
			$base = dirname($this->source->path) . DIRECTORY_SEPARATOR;
			
			// Get the full path to the file relative to the source
			$path = $this->source->find($url);			
			
			if($path !== false)
			{
				// File info
				$id = md5($path);
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				$mod_time = filemtime($path);
				$mime = $this->_get_mime($ext);
				
				// If it's a file we can actually use
				if($mime !== false)
				{
					$data = $this->_load($id);
					
					// If the cached version has expired
					if($data === false OR $mod_time > $data->last_modified)
					{
						$data = base64_encode(file_get_contents($path));
						$this->_save($id,$data,$mod_time,false);
					}
					else
					{
						$data = $data->contents;
					}

					$string = 'data:image/'.$mime.';base64,' . $data;
					return "url($string)";
				}
			}
		}
		
		return "url($url)";
	}
	
	/**
	 * Get file MIME-type
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	private function _get_mime($ext)
	{
		if(in_array($ext,$this->_extensions['images']))
		{
			return 'image/'.$ext;
		}
		
		if(in_array($ext,$this->_extensions['fonts']))
		{
			return 'application/octet-stream';
		}
		
		return false;
	}
	
	/**
	 * Caches an encoded image file
	 * @access private
	 * @param $id
	 * @param $data
	 * @return void
	 */
	private function _save($id,$data,$last_modified)
	{
		$this->scaffold->cache->set($id,$data,$last_modified);
	}
	
	/**
	 * Gets a saved data string
	 * @access private
	 * @param $id
	 * @return mixed
	 */
	private function _load($id)
	{
		return $this->scaffold->cache->get($id);
	}
}