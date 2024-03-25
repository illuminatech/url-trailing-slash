Laravel URL Route Trailing Slash
================================

1.1.11 Under Development
------------------------

- Enh #14: Added support for "illuminate/routing" 11.0 (klimov-paul)


1.1.10, June 23, 2023
---------------------

- Bug #12: Fixed incorrect default route name generation for `Route::resource()` with trailing slash (klimov-paul)


1.1.9, February 24, 2023
------------------------

- Enh #11: Added support for "illuminate/routing" 10.0 (klimov-paul)


1.1.8, February 9, 2022
-----------------------

- Enh: Added support for "illuminate/routing" 9.0 (klimov-paul)


1.1.7, November 13, 2020
------------------------

- Bug #8: Fixed `keyResolver` and `sessionResolver` setup for `UrlGenerator` (oheck, klimov-paul)


1.1.6, September 9, 2020
------------------------

- Enh: Added support for "illuminate/routing" 8.0 (klimov-paul)


1.1.5, July 24, 2020
--------------------

- Bug #6: Fixed `Route::$hasTrailingSlash` value loss during route caching with "illuminate/routing" >= 7.0 (klimov-paul)


1.1.4, April 23, 2020
---------------------

- Bug #4: Fixed incompatibility with new route caching of "illuminate/routing" >= 7.0 (klimov-paul)


1.1.3, March 4, 2020
--------------------

- Enh: Added support for "illuminate/routing" 7.0 (klimov-paul)


1.1.2, February 17, 2020
------------------------

- Bug #3: Fix `UrlGenerator::full()` does not respects trailing slash in current request URI (klimov-paul)


1.1.1, October 2, 2019
----------------------

- Enh #1: Added trailing slash options for resource routes definition (klimov-paul)


1.1.0, September 6, 2019
------------------------

- Enh: Added support for "illuminate/routing" 6.0 (klimov-paul)


1.0.1, July 10, 2019
--------------------

- Enh: Added `AllowsUrlTrailingSlash` test case trait for the better unit and feature tests support (klimov-paul)


1.0.0, July 8, 2019
-------------------

- Initial release.
