<?php

// =========================================
// = Scaffold Configuration =
// =========================================

/**
 * The location of the Scaffold system folder
 */
$system = './';

/**
 * Production Mode
 *
 * TRUE for production, FALSE for development. In development the cache is always
 * refreshed each time you reload the CSS. In production, the cache is locked
 * and will only be recache if BOTH of these conditions are met:
 *
 *		1. One of the files in the request has changed
 *		2. The cache lifetime has expired (set below)
 */
$config['production'] = false;

/**
 * Cache Lifetime
 *
 * This value, in seconds, determines how long the cache will last before it
 * tries to recompile the CSS again from the source. This won't affect the
 * the browser cache unless the file has actually changed.
 */
$config['max_age'] = 3600;

/**
 * Load paths
 *
 * Paths for Scaffold to search for files. It will use these paths to try and find
 * @import and url() paths (and others).
 */
$config['load_paths'] = array();

/**
 * PHP gzip compression
 *
 * If you don't want to use the .htaccess to gzip your files, you can have Scaffold
 * do it for you before it outputs the CSS to the browser. Note that this takes longer
 * to do than to just use Apache to compress your components.
 *
 * Set this from 1-9, with 9 being the highest compression, and false being no compression.
 */
$config['output_compression'] = false;

/** 
 * Use ETag
 *
 * You can generate an ETag for the requests made to CSS files. This isn't entirely
 * necessary as we're using the Last-Modified header. If you aren't planning on using
 * ETags for any specific reason, it's best to leave this off.
 *
 * To completely turn off ETags, the .htaccess also contains 'FileETag none'. If you're
 * switching this on, you'll need to remove or comment out that line in the .htaccess.
 *
 * @see http://developer.yahoo.com/performance/rules.html#etags
 */
$config['set_etag'] = false;

/**
 * Set Content-Length
 *
 * You can set the content-length header. Most of the time, this will be sorted out
 * for you by Apache, but you can have PHP set the content-length if need be.
 */
$config['set_content_length'] = false;

// =========================================
// = Extension Configuration =
// =========================================

/** 
 * Enabled extensions
 */
$config['extensions'] = array(
	'AbsoluteUrls',
	'Constants',
	'Embed',
	'Functions',
	'HSL',
	'ImageReplace',
	'Minify',
	'Properties',
	'Random',
	'Import',
	
	# Process-heavy Extensions
	//'Sass',
	//'CSSTidy',
	//'YUICompressor'
);

/**
 * Extensions have their own configuration by using the format:
 * 		$config['Constants']['key'] = value;
 * These are then available within the extension with $this->config.
 */
//$config['AbsoluteUrls']['require_files'] = false;

// =========================================
// = Load Scaffold =
// =========================================

include $system.'/index.php';