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

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $c = new \PHPF\Collection(array(1, 2, 3));

        $this->assertEquals(1, $c->get(0), 'check 1');
        $this->assertEquals(2, $c->get(1), 'check 2');
        $this->assertEquals(3, $c->get(2), 'check 3');

        $this->assertEquals(3, $c->count(), 'check length');
    }
}
