<?php

/**
 * Expands the box-sizing property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_box_sizing($value)
{
	return "
		-moz-box-sizing:$value;
		-webkit-box-sizing:$value;
		-ms-box-sizing:$value;
		box-sizing:$value;
	";
}