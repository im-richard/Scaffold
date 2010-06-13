<?php

/**
 * Expands the border-bottom-right-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_bottom_right_radius($radius)
{
	return "
		-webkit-border-bottom-right-radius:$radius;
		-moz-border-radius-bottomright:$radius;
	";
}