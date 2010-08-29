<?php
/**
 * Scaffold_Extension_SingleLineComments
 *
 * Allows the use of single-line comments in CSS.
 *
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_SingleLineComments extends Scaffold_Extension
{
	public function pre_format($source,$scaffold)
	{
		$source->contents = $this->helper->css->remove_inline_comments($source->contents);
	}
}