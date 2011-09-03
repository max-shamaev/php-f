<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * PHP version 5.3.0
 * 
 * @author  Maxim Shamaev (maxim.shamaev@gmail.com) 
 * @license Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0
 * @version $id$
 * @link    https://github.com/max-shamaev/php-f
 * @since   1.0.0
 */

spl_autoload_register(
    function ($name) {
        if ('PHPF\\' == substr($name, 0, 5)) {
            $path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
            require_once $path;
        }
    }
);

