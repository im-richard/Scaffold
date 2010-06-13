<?php

/**
 * Expands the column-count property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_column_count($count)
{
	return "
		-moz-column-count:$count;
		-webkit-column-count:$count;
	";
}