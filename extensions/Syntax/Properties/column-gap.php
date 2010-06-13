<?php

/**
 * Expands the column-gap property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_column_gap($gap)
{
	return "
		-moz-column-gap:$gap;
		-webkit-column-gap:$gap;
	";
}