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

    public function testBuild()
    {
        // Object's method
        $t = new ClosureTestObject;
        $this->assertEquals(3, \PHPF\Closure::build(array($t, 'm'))->call(1, 1, 1), 'check call');
    }

    public function testGetAsCallable()
    {
        // Object's method
        $t = new ClosureTestObject;
        $this->assertEquals(array($t, 'm'), \PHPF\Closure::getAsCallable(array($t, 'm')), 'check callable as object + method');

        // Class static method
        $this->assertEquals(array('ClosureTestObject', 's'), \PHPF\Closure::getAsCallable(array('ClosureTestObject', 's')), 'check callable as class + static method');

        // Function
        $this->assertEquals('ClosureTestFunction', \PHPF\Closure::getAsCallable('ClosureTestFunction'), 'check callable as function name');

        // Lambda function after create_function() fabric
        $func = create_function('$a, $b, $c', 'return $a + $b + $c;');
        $this->assertEquals($func, \PHPF\Closure::getAsCallable($func), 'check callable as labda function');

        // Closure
        $func = function ($a, $b, $c)
        {
            return $a + $b + $c;
        };
        $this->assertEquals($func, \PHPF\Closure::getAsCallable($func), 'check callable as Closure');

        // Class static method as string
        $this->assertEquals('ClosureTestObject::s', \PHPF\Closure::getAsCallable('ClosureTestObject::s'), 'check callable as class + static method string');

        // \PHPF\Closure
        $t = new ClosureTestObject;
        $obj = new \PHPF\Closure(array($t, 'm'));
        $this->assertInstanceOf('Closure', \PHPF\Closure::getAsCallable($obj), 'check callable as \PHPF\Closure');

        // Non-callable
        $found = false;
        try {
            \PHPF\Closure::getAsCallable(123);
        } catch (\InvalidArgumentException $e) {
            $found = true;
        }

        if (!$found) {
            $this->fail('Check wrong type');
        }

    }

    public function testBuildByConcat()
    {
        $obj = \PHPF\Closure::buildByConcat('ClosureTestFunction2', 'ClosureTestFunction2', 'ClosureTestFunction2');
        $this->assertInstanceOf('PHPF\Closure', $obj, 'check class');
        $this->assertEquals(array(4, 7, 10), $obj->call(1, 1, 1), 'check call');

        $obj = \PHPF\Closure::buildByConcat('ClosureTestFunction2', 'ClosureTestFunction2', 'ClosureTestFunctionErr');
        $this->assertEquals(array(3, 5, 7), $obj->call(1, 1, 1), 'check call #2');

        // Less callable arguments
        $found = false;
        try {
            \PHPF\Closure::buildByConcat('ClosureTestFunction2', 'ClosureTestFunctionErr', 'ClosureTestFunctionErr');
        } catch (\InvalidArgumentException $e) {
            $found = true;
        }

        if (!$found) {
            $this->fail('Check less arguments');
        }
    }

    public function testGetClosure()
    {
        $t = new ClosureTestObject;
        $obj = new \PHPF\Closure(array($t, 'm'));
        $this->assertInstanceOf('Closure', $obj->getCLosure(), 'check type (after array)');

        $func = function ($a, $b, $c)
        {
            return $a + $b + $c;
        };
        $obj = new \PHPF\Closure($func);
        $this->assertInstanceOf('Closure', $obj->getCLosure(), 'check type (after \Closure)');
    }

    public function testGetReflection()
    {
        // Object's method
        $t = new ClosureTestObject;
        $obj = new \PHPF\Closure(array($t, 'm'));
        $this->assertInstanceOf('ReflectionMethod', $obj->getReflection(), 'check method #1');

        // Class static method
        $obj = new \PHPF\Closure(array('ClosureTestObject', 's'));
        $this->assertInstanceOf('ReflectionMethod', $obj->getReflection(), 'check method #2');

        // Function
        $obj = new \PHPF\Closure('ClosureTestFunction');
        $this->assertInstanceOf('ReflectionFunction', $obj->getReflection(), 'check function #1');

        // Lambda function after create_function() fabric
        $func = create_function('$a, $b, $c', 'return $a + $b + $c;');
        $obj = new \PHPF\Closure($func);
        $this->assertInstanceOf('ReflectionFunction', $obj->getReflection(), 'check function #2');

        // Closure
        $func = function ($a, $b, $c)
        {
            return $a + $b + $c;
        };
        $obj = new \PHPF\Closure($func);
        $this->assertInstanceOf('ReflectionFunction', $obj->getReflection(), 'check function #3');

        // Class static method as string
        $obj = new \PHPF\Closure('ClosureTestObject::s');
        $this->assertInstanceOf('ReflectionMethod', $obj->getReflection(), 'check method #3');
    }

    public function testFlip()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');
        $this->assertEquals(array(2, 4, 6), $obj->call(1, 2, 3), 'check result');
        $this->assertEquals(array(4, 4, 4), $obj->flip()->call(1, 2, 3), 'check flipped result');
    }

    public function testAssignDefaultArgs()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');

        $obj->assignDefaultArgs(2, 2, 2);

        $this->assertEquals(array(3, 4, 5), $obj->call(), 'check result with default arguments');
        $this->assertEquals(array(2, 4, 5), $obj->call(1), 'check result with default arguments (1 / 2)');
        $this->assertEquals(array(2, 3, 5), $obj->call(1, 1), 'check result with default arguments (2 / 1)');
        $this->assertEquals(array(2, 3, 4), $obj->call(1, 1, 1), 'check result with default arguments (3 / 0)');
    }

    public function testAssignArgs()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');
        $obj->assignArgs(2, 2, 2);

        $this->assertEquals(array(3, 4, 5), $obj->call(), 'check result with arguments');
        $this->assertEquals(array(3, 4, 5), $obj->call(1), 'check result with arguments (1 / 2)');
        $this->assertEquals(array(3, 4, 5), $obj->call(1, 1), 'check result with arguments (2 / 1)');
        $this->assertEquals(array(3, 4, 5), $obj->call(1, 1, 1), 'check result with arguments (3 / 0)');

        $obj = new \PHPF\Closure('ClosureTestFunction2');
        $obj->assignArgs(2, 2);

        $this->assertEquals(array(3, 4, 4), $obj->call(1, 1, 1), 'check result with arguments (3 / 0) #2');

        $obj = new \PHPF\Closure('ClosureTestFunction2');
        $obj->assignArgs(2);

        $this->assertEquals(array(3, 3, 4), $obj->call(1, 1, 1), 'check result with arguments (3 / 0) #3');
    }

    public function testConcat()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');

        $this->assertEquals(array(3, 5, 7), $obj->concat('ClosureTestFunction2')->call(1, 1, 1), 'check result');

        $obj = new \PHPF\Closure('ClosureTestFunction2');

        $this->assertEquals(array(4, 7, 10), $obj->concat('ClosureTestFunction2', 'ClosureTestFunction2')->call(1, 1, 1), 'check result #2');
    }

    public function testCallArray()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');
        $this->assertEquals(array(2, 3, 4), $obj->call(1, 1, 1), 'check result');
        $this->assertEquals(array(2, 3, 4), $obj->callArray(array(1, 1, 1)), 'check result with array');
    }

    public function testFork()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');

        $this->assertEquals(array(3, 5, 7), $obj->fork()->concat('ClosureTestFunction2')->call(1, 1, 1), 'check result (forked)');
        $this->assertEquals(array(2, 3, 4), $obj->call(1, 1, 1), 'check result');
    }

    public function testCloneOuter()
    {
        $obj = new \PHPF\Closure('ClosureTestFunction2');

        $clone = null;
        $this->assertEquals(array(3, 5, 7), $obj->cloneOuter($clone)->concat('ClosureTestFunction2')->call(1, 1, 1), 'check result');
        $this->assertEquals(array(2, 3, 4), $clone->call(1, 1, 1), 'check result (cloned)');

        $obj->cloneOuter($clone);
        $this->assertEquals(array(3, 5, 7), $clone->call(1, 1, 1), 'check result (new cloned)');
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

function ClosureTestFunction2($a, $b, $c)
{
    $a++;
    $b += 2;
    $c += 3;

    return array($a, $b, $c);
}

