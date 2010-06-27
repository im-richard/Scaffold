# Absolute URLS

This extension turns all urls found within @imports and url() functions into absolute paths. This helps to avoid broken paths when accessing CSS files directly through Scaffold:

	http://scaffold/system/scaffold.php?file=/stylesheets/example.css

The CSS will be looking for files relative to `/system/scaffold.php` rather than `/stylesheets/example.css`, so images and links will break in the browser. 

Note: This won't happen if you're accessing the CSS through the .htaccess and automatically parsing the CSS as your URLs will still look like this:

	http://scaffold/example.css

So the browser will still look for files in the right location.