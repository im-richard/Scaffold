<?php
/**
 * Scaffold_Extension_Embed
 *
 * Uses the Function extension to create a new CSS function called embed().
 * This lets you embed images and other url() data in the CSS as base64 encoded strings.
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
	 * Used to decide which mime type to use
	 * @var array
	 */
	private $_extensions = array
	(
		'images' 	=> array('gif','jpg','jpeg','png'),
		'fonts' 	=> array('otf','ttf','woff')
	);

	/**
	 * Parses embed() functions within the CSS.
	 * @access public
	 * @param $source
	 * @param $scaffold
	 * @return string
	 */
	public function process($source,$scaffold)
	{
		// We can only embed for file sources
		if($source->type != 'file') return;
		
		foreach($scaffold->helper->css->find_functions('embed',$source->contents) as $found)
		{
			// Get the full path to the file relative to the source
			$path = $source->find($url);			
			
			// The file doesn't exist
			if($path === false)
				continue;

			$id 		= 'embeds/'.md5($path);
			$ext 		= pathinfo($path, PATHINFO_EXTENSION);
			$mod_time 	= filemtime($path);
			$mime 		= $this->_get_mime($ext);
			
			// If it's a file we can actually use
			if($mime === false)
				continue;
			
			// Try and load it from the cache
			$data = $scaffold->cache->get($id);
			
			// If the cached version has expired
			if($data === false OR $mod_time > $data->last_modified)
			{
				$data = base64_encode(file_get_contents($path));
				$scaffold->cache->set($id,$data,$mod_time,false);
			}
			else
			{
				$data = $data->contents;
			}
			
			$string = "url(data:$mime;base64,$data)";
			$source->contents = str_replace($found['string'],$string,$source->contents);
		}
	}
	
	/**
	 * Get file MIME-type
	 * @access private
	 * @param $ext
	 * @return mixed
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
}