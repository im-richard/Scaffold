<?php

/**
 * Adds the image-replace property that allows you to automatically
 * replace text with an image on your server.
 *
 * Because functions can't have - in their name, it is replaced
 * with an underscore. The property name is still image-replace
 *
 * @author Anthony Short
 * @param $url
 * @return string
 */
function Scaffold_image_replace($url)
{
	$url = preg_replace('/\s+/','',$url);
	$url = preg_replace('/url\\([\'\"]?|[\'\"]?\)$/', '', $url);

	$path = Scaffold::find_file($url);
	
	if($path === false)
		return '';
																		
	// Get the size of the image file
	$size = GetImageSize($path);
	$width = $size[0];
	$height = $size[1];
	
	// Make sure theres a value so it doesn't break the css
	if(!$width && !$height)
	{
		$width = $height = 0;
	}
	
	// Build the selector
	$properties = "background:url(".Scaffold::url($path).") no-repeat 0 0;
		height:{$height}px;
		width:{$width}px;
		display:block;
		text-indent:-9999px;
		overflow:hidden;";

	return $properties;
}