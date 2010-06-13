<?php

/**
 * Expands the column-width property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_column_width($width)
{
	return "
		-moz-column-width:$width;
		-webkit-column-width:$width;
	";
}