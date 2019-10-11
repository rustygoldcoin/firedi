# FireDI - PHP Dependency Injection (DI)

A light weight PHP dependency injection library featuring automatic, constructor based, dependency resolution. When you need a new object from a class definition just simply ask for it. Any dependencies type hinted on the constructor will be resolved and injected before the class is instantiated into an object.

The whole reason FireDI to exists is to manage and resolve dependencies for you. The Dependency Injection design pattern provides you with the ability to decouple class dependencies across your entire application by resolving these dependencies for you.

Features:

* Automatic constructor based dependency resolution. Dependencies can be resolve automatically for you by type hinting the object you expect to be injected in the constructor.
* Circular dependency detection.
* Automatic object caching. So that when you ask for an object more than once, it is provided without having to run through the process of dependency resolution.
* The Ability to define your own dependencies within the DI container. This works great for mocking dependencies within unit/integration tests.
* Ability to define your own dependencies for the situation where you need to get a class with specific dependencies that are not defined with in the container.

## Install FireDI Using Composer

    composer require ua1-labs/firedi

## Getting Started

Let's say you would like to get an instantiated object `MyClass1`. Well `MyClass1` requires that you pass into its constructor `MyClass2`. FireDI will resolve `MyClass2` and automatically inject it into `MyClass1` and return the instantiated object `MyClass1` to you.

__MyClass1__

    class MyClass1
    {
        public function __construct(MyClass2 $myClass2) {}
    }

__MyClass2__

    class MyClass2 {}

__FireDI In Action__

    // instantiate firedi
    $firedi = new UA1Labs\Fire\Di();

    // obtain an object and have it's dependencies resolved for you
    $myClass1 = $firedi->get('MyClass1');

## Documentation

https://ua1.us/projects/firedi/
