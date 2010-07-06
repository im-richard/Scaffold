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
include $system.'/lib/Environment.php';

/**
 * Set timezone, just in case it isn't set. PHP 5.3+ 
 * throws a tantrum if you try and use time() without
 * this being set.
 */
date_default_timezone_set('GMT');

/**
 * Automatically load any Scaffold Classes
 * @see http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Environment','auto_load'));

/**
 * Catch any exceptions to display a nice error message
 * @see http://au.php.net/manual/en/function.set-exception-handler.php
 */
set_exception_handler(array('Environment','exception_handler'));

/**
 * Catch errors and convert them into exceptions
 * @see http://au.php.net/manual/en/function.set-error-handler.php
 */
set_error_handler(array('Environment','error_handler'));

/**
 * Catches errors not caught by the other handlers like E_PARSE
 * @see http://au.php.net/manual/en/function.register-shutdown-function.php
 */
register_shutdown_function(array('Environment', 'shutdown_handler'));

/** 
 * Set the view to use for errors and exceptions
 */
Environment::set_view(realpath($system.'/views/error.php'));

// =========================================
// = Start the scaffolding magic  =
// =========================================

// Make sure the config var is set
if(!isset($config))
	$config = array();

// The container creates Scaffold objects
$container = new Scaffold_Container($system,$config);

// This is where the magic happens
$scaffold = $container->build();

// Get the requested source
if(isset($_GET['file']))
{
	$source = new Scaffold_Source_File( $scaffold->loader->find_file($_GET['file']) );
}
elseif(isset($_GET['url']))
{
	$source = new Scaffold_Source_Url($_GET['url']);
}
elseif(isset($_GET['string']))
{
	$source = new Scaffold_Source_String($_GET['string']);
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