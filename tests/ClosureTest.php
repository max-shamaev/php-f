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

class ClosureTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        // Object's method
        $t = new ClosureTestObject;
        $obj = new \PHPF\Closure(array($t, 'm'));
        $this->assertEquals(3, $obj->call(1, 1, 1), 'check call #1');

        // Class static method
        $obj = new \PHPF\Closure(array('ClosureTestObject', 's'));
        $this->assertEquals(3, $obj->call(1, 1, 1), 'check call #2');

        // Function
        $obj = new \PHPF\Closure('ClosureTestFunction');
        $this->assertEquals(3, $obj->call(1, 1, 1), 'check call #3');

        // Lambda function after create_function() fabric
        $func = create_function('$a, $b, $c', 'return $a + $b + $c;');
        $obj = new \PHPF\Closure($func);
        $this->assertEquals(3, $obj->call(1, 1, 1), 'check call #4');

        // Closure
        $func = function ($a, $b, $c)
        {
            return $a + $b + $c;
        };
        $obj = new \PHPF\Closure($func);
        $this->assertEquals(3, $obj->call(1, 1, 1), 'check call #5');

        // Class static method as string
        $obj = new \PHPF\Closure('ClosureTestObject::s');
        $this->assertEquals(3, $obj->call(1, 1, 1), 'check call #6');

        // Non-callable
        $found = false;
        try {
            new \PHPF\Closure(123);
        } catch (\InvalidArgumentException $e) {
            $found = true;
        }

        if (!$found) {
            $this->fail('Check wrong type');
        }

        $found = false;
        try {
            new \PHPF\Closure('ClosureTestFunctionNone');
        } catch (\InvalidArgumentException $e) {
            $found = true;
        }

        if (!$found) {
            $this->fail('Check missing function');
        }

        $found = false;
        try {
            new \PHPF\Closure('ClosureTestObjectNone::s');
        } catch (\InvalidArgumentException $e) {
            $found = true;
        }

        if (!$found) {
            $this->fail('Check missing class');
        }

        $found = false;
        try {
            new \PHPF\Closure(array(123, 'm'));
        } catch (\InvalidArgumentException $e) {
            $found = true;
        }

        if (!$found) {
            $this->fail('Check missing object');
        }

    }
}

class ClosureTestObject
{
    public function m($a, $b, $c)
    {
        return $a + $b + $c;
    }

    public static function s($a, $b, $c)
    {
        return $a + $b + $c;
    }

}

function ClosureTestFunction($a, $b, $c)
{
    return $a + $b + $c;
}
