<?php
/**
 * Scaffold_Extension_CSSTidy
 *
 * Parses the CSS through CSSTidy
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
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array();
	
	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function initialize($source,$scaffold)
	{
		require_once dirname(__FILE__) . '/csstidy-1.3/data.inc.php';
		require_once dirname(__FILE__) . '/csstidy-1.3/lang.inc.php';
		require_once dirname(__FILE__) . '/csstidy-1.3/class.csstidy_optimise.php';
		require_once dirname(__FILE__) . '/csstidy-1.3/class.csstidy_print.php';
		require_once dirname(__FILE__) . '/csstidy-1.3/class.csstidy.php';
	}
	
	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function post_format($source,$scaffold)
	{
		$css = new csstidy();
		
		$css->set_cfg('case_properties',false);
		$css->set_cfg('lowercase_s',true);
		$css->set_cfg('compress_colors',false);
		$css->set_cfg('compress_font-weight',false);
		$css->set_cfg('merge_selectors', true);
		$css->set_cfg('optimise_shorthands',true);
		$css->set_cfg('remove_bslash',false);
		$css->set_cfg('preserve_css',true);
		$css->set_cfg('sort_selectors',true);
		$css->set_cfg('sort_properties',true);
		$css->set_cfg('remove_last_;',true);
		$css->set_cfg('discard_invalid_properties',true);
		$css->set_cfg('css_level','2.1');
		$css->set_cfg('timestamp',false);
		
		$css->load_template('highest_compression');
		
		$result = $css->parse($source->contents);
		$output = $css->print->plain();

		$source->contents = $output;
	}
}