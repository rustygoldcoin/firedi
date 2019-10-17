# Release Changes
* 2.1.0
    * Move firetest and firebug into require-dev in composer.json
    * Rename Di::put() to Di::set() and update the logic so that it will accept a closure to return the object.
    * Implement the PSR-11 Container Interface standard
    * Fix all docblocks. Add leading "\" to classes. Remove "_" from private variables/methods.
    * Include MIT License
* 2.0.1
    * Fixes for broken FireTests with the integration of FIreTest 2.0
    * Adding RELEASE.md for tracking changes
    * Add current release version to composer.json
* 2.0.0
    * Complete overhaul from the Ulfberht library.
    * Update the code base to the latest Fire library standards for UA1 Labs.
    * Initial Release of FireDI.
