h3. Scaffold is a CSS pre-processor built in PHP that allows developers to easily create custom extensions to CSS and reduce the amount of time they spend writing code. 

Out of the box, you get:

* Nested Selectors
* Variables
* Minify/YUI/CSSTidy compression
* Automatically parse files through SASS.
* Combining of files
* Data URI embedding of images/fonts
* Custom CSS functions like @random()@ and @hsla()@
* Custom CSS properties like @image-replace:url();@
* Variables powered by XML
* Mixins

All of this is backed by a strong file cache, so you don't need to worry about the hit to the server. In addition, Scaffold will serve your CSS to the browser with the correct caching headers.  

* Gzip components
* Sets a far-future expires header
* Configures ETags

h2. How it works

Requests to CSS files are made through Scaffold:

<code>
  <link href="scaffold/parse.php?file=/stylesheets/master.css" rel="stylesheet"/>
</code>

You need to tell @parse.php@ the location of a CSS file, and it will do all the work for you. Alternatively, you can [[use a .htaccess file automatically parse CSS files in a directory|Automatically parsing files with a .htaccess file]].

If the file has changed, Scaffold will recache the file, otherwise it will just fetch the processed file from the cache and return that.

h2. Requirements

* PHP 5.2

h2. Setup

<a href="http://github.com/anthonyshort/Scaffold/zipball/master">Download the latest version</a> and place the _scaffold/_ folder somewhere on your webserver.

The @parse.php@ file needs to be accessible to the web, so either put the _scaffold_ folder in your web directory, or move parse.php and change the @$system@ variable inside this file to point to the scaffold directory. @parse.php@ contains the [[configuration options|Configuration]] for Scaffold. 

Now in your HTML reference CSS files by going through the @parse.php@ file:

<code>
  <link href="path/to/parse.php?file=/path/to/stylesheet.css" rel="stylesheet"/>
</code>

The file parameter can point to the CSS file using either an *absolute path, a path relative to parse.php or you can add your stylesheet directory* to the @load_paths@ array in @parse.php@.

h2. Documentation

<a href="http://github.com/anthonyshort/Scaffold/wiki">See the wiki</a> for more detailed documentation.

h2. License

Copyright (c) 2010 Anthony Short

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. No attribution is required by products that make use of this software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the name(s) of the above copyright holders shall not be used in advertising or otherwise to promote the sale, use or other dealings in this Software without prior written authorization.

Contributors to this project agree to grant all rights to the copyright holder of the primary product. Attribution is maintained in the source control history of the product.
