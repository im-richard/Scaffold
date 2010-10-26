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

// The container creates Scaffold objects
$Container = new Scaffold_Container($system,$config);

// This is where the magic happens
$Scaffold = $Container->build();

// Get the sources
$Source = $Scaffold->getSource(null,$config);

// Compiles the source object
$Source = $Scaffold->compile($Source);

// Use the result to render it to the browser. Hooray!
$Scaffold->render($Source);