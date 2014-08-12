Elmo
----

# Why

Simplicity. Elmo is a super lightweight, single-file, scaffold for creating static web features. You don't need to install anything (LAMP stack is fine), don't need to learn a templating language, you don't need a package manager, and you're ~~free~~ encouraged to hack it. At its core, Elmo is less than 200 lines that can be run dynamically for development, or output to static files for production.  

The name comes from [the natural static electricity phenomenon, St. Elmo's Fire](http://en.wikipedia.org/wiki/St._Elmo's_fire).

# Usage

### Routes

Unpack Elmo, and point an Apache server at the Public directory. Add routes (pages) to your project by simply creating php files inside the Routes directory. All files are resolved to clean urls. 

	Routes/
	 - index.php
	 - about.php
	 - products/
	 	- index.php
	 	- flashlight.php
	 	- candle.php
	 	
The above structure would resolve to the following URLs:

	/
	/about
	/products
	/products/flashlight
	/products/candle
	

### Layouts

Layouts are simply html templates. Route files are nested inside layouts, no, there's no silly templating language, turns out PHP is capable of outputting strings too! Here's an example of a basic layout:

	# File: Layouts/master.php
	<html>
	<head>
		<title>Sample Layout</title>
	<body>
		<?php include $this->route ?>
	</body>
	</html>


 Only one layout may be used per route. Any php variables defined in a layout are accessible within the route file as well. As a bonus, route files can define what layout they should be rendered with. 
 
 	# File: Routes/products/index.php
 	<?php $this->setLayout('products'); ?>
 	<h1>Available Products</h1>
 	...

The above code sample would render the route using Layouts/products.php layout file.


### Output

Once you're development is complete, pop open a terminal, navigate to your project and execute:

	php Elmo.php ~/your_output_directory
	
Done.

### Notes

This was written in 1/2 a morning, be gracious. Pull requests gladly accepted.