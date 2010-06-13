<?php

/**
 * Expands the border-top-right-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_top_right_radius($radius)
{
	return "
		-webkit-border-top-right-radius:$radius;
		-moz-border-radius-topright:$radius;
	";
}