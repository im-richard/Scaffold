<?php
/**
 * Scaffold_Extension_Exception
 *
 * Custom exception for extensions.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Exception extends Exception
{
	public function __construct($title,$message,$file = false,$line = false)
	{
		$this->title = $title;
		
		if($file !== false)
		{
			$this->file = $file;
		}
		
		if($line !== false)
		{
			$this->line = $line;
		}
		
		parent::__construct($message,0);
	}
}