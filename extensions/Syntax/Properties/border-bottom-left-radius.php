<?php

/**
 * Expands the border-bottom-left-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_bottom_left_radius($radius)
{
	return "
		-webkit-border-bottom-left-radius:$radius;
		-moz-border-radius-bottomleft:$radius;
	";
}