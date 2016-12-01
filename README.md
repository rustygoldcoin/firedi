# Ulfberht - PHP Dependency Injection (DI)

This PHP DI Tool was named after a Viking Sword called "The Ulfberht". "The Ulfberht" was found to be the lightest and strongest weapon of its time. The development of this tool was modeled after the same principles that were implemented during the construction of the Ulfberht Sword. Simply put, be the lightest and strongest tool ever created to fight against the evils  of dependency mapping that live deep within our PHP applications.

Features:

* Automated dependency resolution
* Circular dependency detection
* Supports both `factory` and `singleton` construct patterns
* No more anonymous functions for creating factories =)

### Install Ulfberht Using Composer:

`composer require ua1-labs/ulfberht`

### Getting Started:

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

### Automatic Dependency Resolution:

The whole reason Ulfberht exists is to manage and resolve dependencies for you. to be able to have all class dependencies resolved for you automatically. The Dependency Injection design pattern provides you with the ability to decouple class dependencies across your entire application by resolving these dependencies for you.

**How It Works**

Whenever you register a new dependency class with Ulfberht, Ulfberht will take a look at your class constructor and determine class dependencies by the type hints you define on each parameter you require. Ulfberht then keeps track of these dependencies and attempts to resolve these when you request the instance object from Ulfberht using `ulfbhert\core\ulfberht::get()`. For Example:

    class ClassA {

        public function __construct(ClassB $dep1, ClassC $dep2) {
            $this->dep1 = $dep1;
            $this->dep2 = $dep2;
        }

    }

Assuming that `ClassA`, `ClassB`, and `ClassC` was registered with Ulfberht, like so:

    ulfberht()
        ->factory('ClassA')
        ->factory('ClassB')
        ->factory('ClassC');

Notice in the example above, `ClassA` expects an instance of `ClassB` to be passed in as the first paramater and an instance of `ClassC` passed in as the second parameter. As described above, when you registered `ClassA` with Ulfberht, the framework already determined that `ClassB` and `ClassC` are dependencies of `ClassA`. So, when you go to ask Ulfberht to resolve `ClassA` it already knows that `ClassB` and `ClassC` need to be resolved first and passed into `ClassA`.

    $ClassA = ulfberht()->get('ClassA');
    var_dump($ClassA);

Will result in:

    class ClassA#14 (2) {
        public $dep1 => class ClassB#11 (0) {}
        public $dep2 => class ClassC#13 (0) {}
    }

**Detecting Circular Dependencies**

Whenever you request an instance object a registered class from Ulfberht, Ulfberht does a circular dependency check to ensure that non of your dependencies for the given class. If a circular dependency exists, this creates a infinite logic loop which results in a instance object that cannot be resolved. So if `A` => `B` => `C` => `A`. Because `A` depends on `C` and `C` depends on `A` we have a circular dependency and it cannot be resolved. When Ulfberht detects a circular dependency, Ulfberht will throw an Exception.

### Construct Patterns - Factory Vs. Singleton:

When registering a class with Ulfberht, you have to consider whether you want to register the class as a Factory `ulfberht::factory()` or Singleton `ulfberht::singleton()` construct type. When trying to determine which construct type to use you should answer two questions.

1. Do I need a new instance of an object returned every time I get it from Ulfberht? - Use Factory.
2. Do I need a the same instance of an object returned every time I get it from Ulfberht? - Use Singleton.

### Library API Documentation

*class ulfberht\core\ulfberht*

* **ulfbhert::instance()** Gets the singleton instance of this class.
    * returns - ulfberht\core\ulfberht - The ulfberht instance object

* **ulfberht::singleton($className)** This method is used to define a service that returns a singleton instance of the service that was defined.
    * $className - The name of the class you would like to register
    * returns - ulfberht\core\ulfberht - The ulfberht instance object

* **ulfberht::factory($className)** This method is used to define a service that returns a new instance of the service that was defined everytime it is called.
    * $className - The name of the class you would like to register
    * returns - ulfberht\core\ulfberht - The ulfberht instance object

* **ulfberht::get($className)**
    * $className - The name of the class you would like to register
    * returns - object - an instance of the class you requested

* **ulfberht::has($className)**  This method is used to check to see if a class has been registered to be used with ulfberht.
    * $className - The name of the class you would like to register
    * returns - boolean

* **ulfberht::destroy()** Destroy the ulfberht instance.
    * returns - void

### License:

MIT license - http://www.opensource.org/licenses/mit-license.php
