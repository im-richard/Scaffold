<?php

/**
 * Adds a border-left-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_left_radius($radius)
{
	return "
		-webkit-border-top-left-radius:$radius;
		-moz-border-radius-topleft:$radius;
		-webkit-border-bottom-left-radius:$radius;
		-moz-border-radius-bottomleft:$radius;
	";
}