<?php

/**
 * Expands the border-radius property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_border_radius($radius)
{
	return "
		-webkit-border-radius:$radius;
		-moz-border-radius:$radius;
		border-radius:$radius;
	";
}