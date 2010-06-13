<?php

/**
 * Expands the background-size property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_background_size($value)
{
	return "
		-o-background-size:$value;
		-webkit-background-size:$value;
		-khtml-background-size:$value;
		-moz-background-size:$value;
	";
}