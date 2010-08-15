<?php
/**
 * Scaffold_Extension_ImageReplace
 *
 * Easily image-replace images in the CSS. You don't have to 
 * get the height and width of the file manually, it will calculate it for it 
 * and add the properties.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_ImageReplace extends Scaffold_Extension
{	
	/**
	 * Registers the image-replace property
	 * @access public
	 * @param $properties Scaffold_Extension_Properties
	 * @return array
	 */
	public function register_property($properties)
	{
		$properties->register('image-replace',array($this,'image_replace'));
	}
	
	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function initialize($source,$scaffold)
	{
		$this->source = $source;
	}
	
	/**
	 * Parses image-replace properties
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function image_replace($value)
	{
		$url = preg_match('/
				(?:url\(\s*)?      	 # maybe url(
				[\'"]?               # maybe quote
				([^\'\"\)]*)                # 1 = URI
				[\'"]?               # maybe end quote
				(?:\\s*\\))?         # maybe )
			/xs',
			$value,
			$match
		);
		
		if($match)
		{
			$url = $match[1];
			
			// Get the size of the image file
			$size = GetImageSize($this->source->find($url));
			$width = $size[0];
			$height = $size[1];
			
			// Make sure theres a value so it doesn't break the css
			if(!$width && !$height)
			{
				$width = $height = 0;
			}
			
			// Build the selector
			$properties = 'background:url('.$url.') no-repeat 0 0;height:0;padding-top:'.$height.'px;width:'.$width.'px;display:block;text-indent:-9999px;overflow:hidden;';
		
			return $properties;
		}
		
		return false;
	}
}