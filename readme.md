# OpenFlame Framework

The OpenFlame Framework is a collection of scripts used to power OpenFlame CMS and FireTracker. It is a simple multi-purpose PHP5 framework that can be used for any purpose, and is released under the MIT License to facilitate widespread use.

**License**: *MIT License* - <http://opensource.org/licenses/mit-license.php>

**Copyright**: (c) 2010 -- OpenFlameCMS.com

This library generously provided for free by the OpenFlame CMS Development Team.

## Contents

All files within the OpenFlame/ directory are named by the class they contain. 

Included files:

* loader.php - (*File that loads all classes automatically*)
* OpenFlame/Of.php - (*Environment class which will store runtime configurations and object instances*)
* OpenFlame/OfDb.php - (*Doctrine integration layer*)
* OpenFlame/OfException.php - (*Exception classes used within the OpenFlame Framework*)
* OpenFlame/OfFile.php - (*File upload handler class specifically for file uploading*)
* OpenFlame/OfHandler.php - (*Provides a tasteful exception handler for uncaught (and even caught) exceptions*)
* OpenFlame/OfHash.php - (*Provides a secure hashing algorithm using a modified version of phpass*)
* OpenFlame/OfInput.php - (*Input retrieval class, see docs within the file*)
* OpenFlame/OfJSON.php - (*JSON interface class, which makes it easier to work with JSON files*)
* OpenFlame/OfTwig.php - (*Twig integration class, enables easy template variable management*)
* OpenFlame/OfUrlHander.php - (*Provides "pretty" urls functionality*)
* OpenFlame/OfUser.php - (*Wrapper for native PHP sessions*)
