<?php

/**
 * Adds a border-bottom radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_bottom_radius($radius)
{
	return "
		-webkit-border-bottom-right-radius:$radius;
		-moz-border-radius-bottomright:$radius;
		-webkit-border-bottom-left-radius:$radius;
		-moz-border-radius-bottomleft:$radius;
	";
}