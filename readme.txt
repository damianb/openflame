OpenFlame Framework
Released under http://opensource.org/licenses/mit-license.php The MIT Licence
Developed by OpenFlameCMS.com

The OpenFlame Framework is a collection of scripts used to power the OpenFlame CMS and FireTracker. It is
a multi-purpose, PHP5 framework that can be used for any purpose, and is released under The MIT Licence to 
allow for multiple uses of it.

All files within the OpenFlame/ directory are nammed by the class they contain. 

Included files:
loader.php						File that loads all classes automatically
OpenFlame/Of					Contains the empty Of class, will be used as static to contain all our objects
OpenFlame/OfUrlHander.php		Contains the OfUrlHandler class, allows for "pretty" urls handled mostly by PHP
OpenFlame/OfUser.php			Contains the OfUser class, acts a wrapper for PHP sessions and enhances it's security
OpenFlame/OfDb.php				Wrapper for PDO, gives some better flexibility and useful functions.
OpenFlame/OfInput.php			Input class, see docs within the file.
OpenFlame/OfFile.php			Input class specifically for file uploading