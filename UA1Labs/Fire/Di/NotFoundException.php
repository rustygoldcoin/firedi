<?php

/**
 *    __  _____   ___   __          __
 *   / / / /   | <  /  / /   ____ _/ /_  _____
 *  / / / / /| | / /  / /   / __ `/ __ `/ ___/
 * / /_/ / ___ |/ /  / /___/ /_/ / /_/ (__  )
 * `____/_/  |_/_/  /_____/`__,_/_.___/____/
 *
 * @package FireDI
 * @author UA1 Labs Developers https://ua1.us
 * @copyright Copyright (c) UA1 Labs
 */

namespace UA1Labs\Fire\Di;

use \Exception;
use \Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown from the FireDI library.
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface
{

    const ERROR_NOT_FOUND_IN_CONTAINER = '"%s" could not be resolved by FireDI.';

}
