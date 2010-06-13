<?php

$unique = true;

/**
 * Returns a random number between two other numbers.
 *
 * @author Anthony Short
 * @param $from
 * @param $to
 * @return string
 */
function Scaffold_rand($from,$to)
{
	return rand($from, $to);
}