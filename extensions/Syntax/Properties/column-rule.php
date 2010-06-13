<?php

/**
 * Expands the column-rule property
 *
 * @author Anthony Short
 * @param $value
 * @return string
 */
function Scaffold_column_rule($rule)
{
	return "
		-moz-column-rule:$rule;
		-webkit-column-rule:$rule;
	";
}