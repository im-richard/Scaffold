<?php

/**
 * Expands the box-shadow property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_box_shadow($value)
{
	return "
		-moz-box-shadow:$value;
		-webkit-box-shadow:$value;
		box-shadow:$value;
	";
}