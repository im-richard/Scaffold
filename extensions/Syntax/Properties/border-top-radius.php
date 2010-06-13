<?php

/**
 * Adds a border-top radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_top_radius($radius)
{
	return "
		-webkit-border-top-right-radius:$radius;
		-moz-border-radius-topright:$radius;
		-webkit-border-top-left-radius:$radius;
		-moz-border-radius-topleft:$radius;
	";
}