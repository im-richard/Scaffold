<?php

/**
 * Expands the opacity property to work in all browsers
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_opacity($opacity)
{
	$ie = $opacity * 100;

	return "
		opacity:$opacity;
		-moz-opacity:$opacity;
		-khtml-opacity:$opacity;
		-ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity={$ie})\";
		filter:alpha(opacity={$ie});
	";
}