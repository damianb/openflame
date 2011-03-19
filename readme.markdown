# OpenFlame Framework

The OpenFlame Framework is a collection of scripts used to power OpenFlame CMS and FireTracker. It is a simple multi-purpose PHP5 framework that can be used for any purpose, and is released under the MIT License to facilitate widespread use.

**License**: *MIT License* - <http://opensource.org/licenses/mit-license.php>

**Copyright**: (c) 2010 -- OpenFlameCMS.com

This library generously provided for free by the OpenFlame CMS Development Team.

## WARNING

This branch is no longer maintained by the OpenFlame Development team.  We have moved on and have developed a branch intended for use with PHP 5.3+, as PHP 5.2 has been declared end of life by the PHP development team themselves.  If you are still on PHP 5.2, please consider upgrading ASAP.

## Contents

All files within the src/ directory are named by the class they contain.

Included files:

* Bootstrap.php - (*File that loads all classes automatically*)
* src/Of.php - (*Environment class which will store runtime configurations and object instances*)
* src/OfCLI.php - (*CLI environment object providing methods to easily interact with the user, along with providing easy use of POSIX terminal colors*)
* src/OfCLIHandler.php - (*Error and Exception handler designed for use in a CLI environment*)
* src/OfCache.php - (*Cache interface, provides an abstract layer that can be used with interchangable caching engines*)
* src/OfCacheEngineJSON.php - (*JSON-based cache engine, intended for use by OfCache.php*)
* src/OfDb.php - (*Doctrine integration layer*)
* src/OfException.php - (*Exception classes used within the OpenFlame Framework*)
* src/OfFile.php - (*File upload handler class specifically for file uploading*)
* src/OfForm.php - (*Form security handler, provides methods to protect against CSRF*)
* src/OfHandler.php - (*Provides a tasteful exception handler for uncaught (and even caught) exceptions*)
* src/OfHash.php - (*Provides a secure hashing algorithm using a modified version of phpass*)
* src/OfInput.php - (*Input retrieval class, see docs within the file*)
* src/OfJSON.php - (*JSON interface class, which makes it easier to work with JSON files*)
* src/OfTwig.php - (*Twig integration class, enables easy template variable management*)
* src/OfUrlHander.php - (*Provides "pretty" urls functionality*)
* src/OfUser.php - (*Basic user class with login, time handling, and persistent login functionality*)
* src/OfSession.php - (*Wrapper for native PHP sessions*)

