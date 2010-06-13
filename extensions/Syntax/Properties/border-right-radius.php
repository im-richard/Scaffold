<?php

/**
 * Adds a border-right-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_right_radius($radius)
{
	return "
		-webkit-border-top-right-radius:$radius;
		-moz-border-radius-topright:$radius;
		-webkit-border-bottom-right-radius:$radius;
		-moz-border-radius-bottomright:$radius;
	";
}