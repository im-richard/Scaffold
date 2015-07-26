<?php

/**
 * Scaffold_Helper_Array
 *
 * Helper methods for dealing with arrays
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Helper_Array
{
	/**
	 * Merges any number of arrays / parameters recursively, replacing 
	 * entries with string keys with values from latter arrays. 
	 * If the entry or the next value to be assigned is an array, then it 
	 * automagically treats both arguments as an array.
	 * Numeric entries are appended, not replaced, but only if they are 
	 * unique
	 *
	 * PHP's array_merge_recursive does indeed merge arrays, but it converts
	 * values with duplicate keys to arrays rather than overwriting the value 
	 * in the first array with the duplicate value in the second array, as 
	 * array_merge does. e.g., with array_merge_recursive, this happens 
	 * (documented behavior):
	 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     returns: array('key' => array('org value', 'new value'));
	 * 
	 * calling: result = array_merge_recursive_distinct(a1, a2, ... aN)
	 *
	 * @author <mark dot roduner at gmail dot com>
	 * @link http://www.php.net/manual/en/function.array-merge-recursive.php#96201
	 * @access public
	 * @param $array1, [$array2, $array3, ...]
	 * @return array Resulting array, once all have been merged
	 */
	public function merge_recursive () {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		if(!is_array($base)) $base = empty($base) ? array() : array($base);
		
		foreach($arrays as $append) {
			
			if(!is_array($append)) $append = array($append);
			
			foreach($append as $key => $value) {
				if(!array_key_exists($key, $base) and !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if(is_array($value) or is_array($base[$key])) {
					$base[$key] = $this->merge_recursive($base[$key], $append[$key]);
				} else if(is_numeric($key)) {
					if(!in_array($value, $base)) $base[] = $value;
				} else {
					$base[$key] = $value;
				}
			}
		}
		
		return $base;
	}
	
}