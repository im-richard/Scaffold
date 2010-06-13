<?php

/**
 * Embeds a file in the CSS using Base64
 *
 * @param $file
 * @return string
 */
function Scaffold_embed($file)
{
	$path = Scaffold::find_file( Scaffold_Utils::unquote($file) );
	
	# Couldn't find it
	if($path === false)
		return false;

	$data = 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path));
	
	return "url(" . $data . ")";
} 