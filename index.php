<?php

/**
 * The location of this system folder
 */
$system = dirname(__FILE__).'/';

/**
 * The environment class helps us handle errors
 * and autoloading of classes. It's not required
 * to make Scaffold function, but makes it a bit
 * nicer to use.
 */
include $system.'/lib/Scaffold/Environment.php';

/**
 * Set timezone, just in case it isn't set. PHP 5.3+ 
 * throws a tantrum if you try and use time() without
 * this being set.
 */
date_default_timezone_set('GMT');

/**
 * Automatically load any Scaffold Classes
 */
Scaffold_Environment::auto_load();

/**
 * Let Scaffold handle errors
 */
Scaffold_Environment::handle_errors();

/** 
 * Set the view to use for errors and exceptions
 */
Scaffold_Environment::set_view(realpath($system.'/views/error.php'));

// =========================================
// = Start the scaffolding magic  =
// =========================================

// Make sure the config var is set
if(!isset($config)) $config = array();

// The container creates Scaffold objects
$container = new Scaffold_Container($system,$config);

// This is where the magic happens
$scaffold = $container->build();

// Get the requested source
if(isset($_GET['file']))
{
	$source = new Scaffold_Source_File( $scaffold->helper->load->file($_GET['file']) );
}
elseif(isset($_GET['url']) AND $config['enable_url'] === true)
{
	$source = new Scaffold_Source_Url($_GET['url']);
}
elseif(isset($_GET['string']) AND $config['enable_string'] === true)
{
	$source = new Scaffold_Source_String($_GET['string']);
}
elseif(isset($config['default_source']))
{
	$source = new Scaffold_Source_File($config['default_source']);
}
else
{
	echo 'No source :(';
	exit;
}

// Compiles the source object
$source = $scaffold->compile($source);

// Use the result to render it to the browser. Hooray!
$scaffold->render($source);