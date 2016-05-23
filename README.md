#Ulfberht Dependency Injection MVC

Ulfberht was named after a Viking sword found to be the lightest and strongest sword of its time.

Ulfberht is a lightweight and powerful PHP Dependency Injection Container plus MVC framework. At its core, Ulfberht was developed around the ideas of Service Based Dependency Injection and Modularization. As it turns out, following this pattern gives us the flexibility to create modules that can support any architectural design and provide the base foundation for any application. Ulfberht ships with a basic MVC Module (ulfberht\module\ulfberht) which provides Config, Router, View, Request, and Response objects used to build out full MVC applications.

###Install Ulfberht Using Composer:

`ua1-labs/ulfberht:dev-master`

###Quick Start:

1) Register a module and services using `ulfberht\core\module`:

    use ulfberht\core\module;

    class myModule extends module {

        public function __construct() {
            //when this object builds it will create a new object every time.
            $this->registerFactory('A');
            //when this object builds it will return a cached version of the object.
            $this->registerSingleton('B');
        }

        public function configHook(A $a) {
            //executed during the config process of ulfberht::forge();
        }

        public function runHook(A $a, B $b) {
            //executed during the run process of the ulfbhert::forge();            
        }
    }

    //register the module with ulfberht
    ulfberht()->registerModule('myModule');

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