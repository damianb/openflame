# OpenFlame Framework

## changelog

 - 1.0.0 **initial** release
	 - First public release
 - 1.1.0 **minor** release
	 - Altered the `URL\Builder` component so that GET params can be added to all generated URLs (ticket #54)
	 - Document and cleanup the code for the `Security\Seeder` component (ticket #41)
	 - Overhauled the asset instance and asset manager objects for supporting assets on an external domain (ticket #53)
	 - Optimize the event dispatcher for use with closures, functions, and static method calls
	 - Rework TTL support for the caching system and how file-based caching engines are designed (ticket #50)
		- NOTE: This change breaks backwards compatibility when instantiating the caching engines
	 - Introduce a new object (`Security\Form`) that provides form key creation and validation (ticket #20)
 - 1.1.1 **maintenance** release
	 - Fix minor bug with grabbing the commit ID from within the framework phar
 - 1.1.2 **maintenance** release
	 - Fix bug with `Security\Seeder` component - all randomly generated strings will be returned of the requested length now.
