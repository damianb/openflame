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
	 - Fix bug with `Security\Seeder` component - all randomly generated strings will be returned of the requested length now
 - 1.1.3 **maintenance** release
	 - Fix bug with cache component - removed an old unneeded method from the engine interface
 - 1.2.0 **minor** release
	 - Add in new dependency injection component `Dependency\Injector` (ticket #57)
	 - Refactored the cookie management code and split it out into its own component (ticket #55).
	 - Made microoptimizations in component `Event\Dispatcher`
	 - Added new method `triggerUntilReturn()` in component `Event\Dispatcher` (see commit id d88453eaec for details)
	 - Refactor RouteInstance callback storage, simplify callback execution and remove ability to "call" methods of objects stored in the OpenFlame Framework core
	 - Pass the `$request_url` parameter in method `processRequest()` the component `Router\Router` by reference to allow the end application to obtain the sanitized form without resanitizing it
	 - Remove `OpenFlame\ROOT_PATH` constant check in every class file
	 - Refactor the autoloader to not use the now-defunct `OpenFlame\ROOT_PATH` constant for autoloading; it must now be passed the autoload path upon instantiation
	 - Add new component object `Router\AliasRouter` to expand upon current dynamic routing capabilities
	 - Modify the `Router\RouteInstance` component object to add new route "aliases", which are resolved against the new component `Router\AliasRouter` for a usable callback
