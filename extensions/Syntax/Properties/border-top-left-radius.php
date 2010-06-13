<?php

/**
 * Expands the border-top-left-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_top_left_radius($radius)
{
	return "
		-webkit-border-top-left-radius:$radius;
		-moz-border-radius-topleft:$radius;
	";
}