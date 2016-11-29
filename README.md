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


### License:

MIT license - http://www.opensource.org/licenses/mit-license.php
