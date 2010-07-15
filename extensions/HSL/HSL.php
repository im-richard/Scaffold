<?php
/**
 * Scaffold_Extension_HSL
 *
 * Overrides the default HSL functions and converts them to RGB
 * so that any browser can use them.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_HSL extends Scaffold_Extension
{
	/**
	 * Registers a custom CSS function.
	 * @access public
	 * @param $functions
	 * @return array
	 */
	public function register_function($functions)
	{
		$functions->register('hsl',array($this,'hsl'));
		$functions->register('hsla',array($this,'hsla'));
	}
	
	/**
	 * Replaces the built-in hsl() function and outputs the colour
	 * as a hex colour so that it's supported in every browser.
	 *
	 * @author Olivier Gorzalka
	 * @param $h Hue
	 * @param $s Saturation
	 * @param $l Lightness
	 * @return string
	 */
	public function hsl($h,$s,$l)
	{
		$values = $this->_convert_HSL_to_RGB($h,$s,$l);
		return "rgb(".implode(',',$values).")";
	}
	
	/**
	 * Works the same way as the hsl() function except it also takes an opacity.
	 *
	 * @author Olivier Gorzalka
	 * @param $h Hue
	 * @param $s Saturation
	 * @param $l Lightness
	 * @param $a Alpha
	 * @return string
	 */
	public function hsla($h,$s,$l,$a)
	{
		$values = $this->_convert_HSL_to_RGB($h,$s,$l);
		return "rgba(".implode(',',$values).",$a)";
	}
	
	/**
	 * Converts hsl values in rgb. Returns an array of colors
	 * @access public
	 * @param $h
	 * @param $s
	 * @param l
	 * @return array
	 */
	private function _convert_HSL_to_RGB($h,$s,$l)
	{
		// Make sure none of the values are below 0
		$h = max($h,0);
		$s = max($s,0); 
		$l = max($l,0);
		
		// Make sure the values don't exceed their limit
		$h = min($h,360);
		$s = min($s,100); 
		$l = min($l,100);
		
		$h = intval($h)/360;
		$s = intval($s)/100;
		$l = intval($l)/100;
	
		$rgb = array();
		if ($s == 0) {
		  $r = $g = $b = $l * 255;
		} else {
			$var_h = $h * 6;
			$var_i = floor( $var_h );
			$var_1 = $l * ( 1 - $s );
			$var_2 = $l * ( 1 - $s * ( $var_h - $var_i ) );
			$var_3 = $l * ( 1 - $s * (1 - ( $var_h - $var_i ) ) );
			if		 ($var_i == 0) { $var_r = $l	 ; $var_g = $var_3	; $var_b = $var_1 ; }
			else if	 ($var_i == 1) { $var_r = $var_2 ; $var_g = $l		; $var_b = $var_1 ; }
			else if	 ($var_i == 2) { $var_r = $var_1 ; $var_g = $l		; $var_b = $var_3 ; }
			else if	 ($var_i == 3) { $var_r = $var_1 ; $var_g = $var_2	; $var_b = $l	  ; }
			else if	 ($var_i == 4) { $var_r = $var_3 ; $var_g = $var_1	; $var_b = $l	  ; }
			else				   { $var_r = $l	 ; $var_g = $var_1	; $var_b = $var_2 ; }
			$r = ceil($var_r * 255);
			$g = ceil($var_g * 255);
			$b = ceil($var_b * 255);
		}
		
		return array('r'=>$r,'g'=>$g,'b'=>$b);
	}
}