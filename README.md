#Ulfberht - PHP Dependency Injection (DI)

This PHP DI Tool was named after a Viking Sword called "The Ulfberht". "The Ulfberht" was found to be the lightest and strongest weapon of its time. The development of this tool was modeled after the same principles that were implemented during the construction of the Ulfberht Sword. Simply put, be the lightest and strongest tool ever created to fight against the evils  of dependency mapping that live deep within our PHP applications.

Features:

* Automatic constructor based dependency resolution
* Circular dependency validation
* Supports both `factory` and `singleton` construction patterns

###Install Ulfberht Using Composer:

`composer require ua1-labs/ulfberht`

###Getting Started:

1) Ulfberht is a true singleton class and can be accessed in two different ways:

    $ulfberht = ulfberht\core\ulfberht::instance();

or

    $uflberht = ulfberht();

2) Registering a class to be used by Ulfberht:

    //registering a class as a singleton constructor type
    ulfberht()->singleton('stdClass');

    //registering a class as a factory constructor type
    ulfberht()->factory('stdClass');

3) Get constructed object from Ulfberht:

    $object = ulfberht()->get('stdClass');

---

###Automatic Dependency Injection:

If you have one service that depends on another, ulfberht can automatically inject those services for you:

Service `A` depends on `B`

    //When A is constructed, B will construct first and be passed into
    //A as part of the service resolution process.
    class A {
        public function __construct(B $b) {
            var_dump($b);
        }
    }

---

You can also retrieve a service once you have forged your application with `ulfberht::forge()`

    //forging ulfberht
    ulfberht()->forge();
    //get constructed service
    $a = ulfberht()->get('A');

**More and Better Coming Soon!**

###License:

MIT license - http://www.opensource.org/licenses/mit-license.php
