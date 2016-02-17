#Ulfberht Dependency Injection MVC

Ulfberht was named after a Viking sword found to be the lightest and strongest sword of its time. 

Ulfberht is a lightweight and powerful PHP Dependency Injection Container. At its core, Ulfberht was developed around the ideas of Service Based Dependency Injection and Modularization. As it turns out, following this pattern gives us the flexibility to create modules that can support any architectural design and provide the base foundation for any application. Ulfberht ships with a basic MVC Module (ulfberht\module\ulfberht) which provides Config, Router, View, Request, and Response objects to build out full MVC applications.

###Install z20 Using Composer:
Installation is simple using the Composer Package Manager http://getcomposer.org/

Then run the following command: `composer require ua1-labs/ulfberht:dev-master`<br>
Be sure to include ``

###Quick Start:

1) Register a module:
	
	//registering a module
	$myModuleDependencies = array();
	//by calling the module method, z20 will return the module object.
	$myModule = z20::get()->module('myModule', $myModuleDependencies);

2) Register a service:

	//registering a service
	$myModule->factory('myService', function(){
		return new myService();	
	}

3) Load your module into the z20 runtimme environment

	//load module
	z20::get()->loadModule('myModule');

4) Now you can inject myService everywhere!

	//Inject myService
	$myService = z20::get->injector('myService');

###Automatic Dependency Injection:

If you have one service that depends on another, z20 can automatically inject those services for you:

1) Register a module:

	$myModule = z20::get()->module('myModule')

2) Register your first service

	$myModule->factory('FormValidator', function(){
		return new FormValidator();
	});

3) Register your second service and ask for z20 to inject your first service by naming it the same as the service name. i.e. <code>$FormValidator</code>.

	$myModule->factory('FormProcessor', function($FormValidator) {
		return FormProcessor($FormValidator);
	}

4) Load Module:

	//load module
	z20::get()->loadModule('myModule');

When you ask for the FormProcessor service to be injected so that you may use it, z20 will automatically try and resolve the FormValidator dependency you asked it for.

###License:

MIT license - http://www.opensource.org/licenses/mit-license.php