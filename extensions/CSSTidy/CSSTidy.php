<?php

require_once dirname(__FILE__) . '/csstidy-1.3/data.inc.php';
require_once dirname(__FILE__) . '/csstidy-1.3/lang.inc.php';
require_once dirname(__FILE__) . '/csstidy-1.3/class.csstidy_optimise.php';
require_once dirname(__FILE__) . '/csstidy-1.3/class.csstidy_print.php';
require_once dirname(__FILE__) . '/csstidy-1.3/class.csstidy.php';

/**
 * Scaffold_Extension_CSSTidy
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_CSSTidy extends Scaffold_Extension
{
	/**
	 * @var array
	 */
	public $_defaults = array
	(
		'options' => array
		(
			'case_properties' 				=> false,
			'lowercase_s' 					=> false,
			'compress_colors'				=> false,
			'compress_font-weight' 			=> false,
			'merge_selectors'				=> false,
			'optimise_shorthands'			=> false,
			'remove_bslash'					=> false,
			'preserve_css'					=> true,
			'sort_selectors'				=> false,
			'sort_properties'				=> false,
			'remove_last_;'					=> false,
			'discard_invalid_properties'	=> false,
			'css_level'						=> '2.1',
			'timestamp'						=> false,
		)
		'template' => 'low_compression',
	);

	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function post_format($source,$scaffold)
	{
		$css = new csstidy();
		
		// Set the CSSTidy options
		foreach($this->config['options'] as $key => $value)
		{
			$css->set_cfg($key,$value);
		}
		
		// Set the style template
		$css->load_template($this->config['template']);
		
		// Parse the contents
		$result = $css->parse($source->contents);

		// Set the source content
		$source->contents = $css->print->plain();
	}
}