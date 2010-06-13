<?php

/**
 * Expands the transform property to work in browsers that support it
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_transform($value)
{
	return "
		-webkit-transform:$value;
		-moz-transform:$value;
		transform:$value;
	";
}