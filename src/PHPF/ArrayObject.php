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

namespace PHPF;

/**
 * Array object 
 * 
 * @since 1.0.0
 */
class ArrayObject extends \ArrayObject
{
    /**
     * Constructor
     * 
     * @param mixed   $input         Input
     * @param integer $flags         Flags
     * @param string  $iteratorClass Iterato class
     *  
     * @return void
     * @since  1.0.0
     */
    public function __construct($input, $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\ArrayIterator')
    {
        parent::__construct($input, $flags, $iteratorClass);
    }

    /**
     * Set flags - FLAGS CHANGES IS FORBIDDEN
     * 
     * @return void
     * @since  1.0.0
     */
    public function setFlags()
    {
    }

    // {{{ Structure changes

    public function append($value)
    {
        parent::append($value);

        return $this;
    }

    /**
     * Shift first element
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function shift()
    {
        $value = null;

        foreach ($this as $key => $value) {
            $this->offsetUnset($key);
            break;
        }

        return $value;
    }

    /**
     * Add first element
     * 
     * @param mixed $value Value_
     *  
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function unshift($value)
    {
        $data = $this->getArrayCopy();
        array_unshift($data, $value);
        $this->exchangeArray($data);

        return $this;

    }

    /**
     * Pop last element
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function pop()
    {
        $value = null;

        foreach ($this as $key => $value) {
        }

        $this->offsetUnset($key);

        return $value;
    }

    /**
     * Push element
     * 
     * @param mixed $value Value
     *  
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function push($value)
    {
        return $this->append($value);
    }

    /**
     * Merge current array ant some arrays from arguments
     * 
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function merge()
    {
        $data = $this->getArrayCopy();
        $args = func_get_args();
        array_unshift($args, $data);
        $data = call_user_func_array('array_merge', $args);
        $this->exchangeArray($data);

        return $this; 
    }

    // }}}

    // {{{ Sort

    public function asort()
    {
        parent:asort();

        return $this;
    }

    public function ksort()
    {
        parent::ksort();

        return $this;
    }

    public function natcasesort()
    {
        parent::natcasesort();

        return $this;
    }

    public function natsort()
    {
        parent::natsort();

        return $this;
    }

    public function uasort()
    {
        parent::uasort();

        return $this;
    }

    public function uksort()
    {
        parent::uksort();

        return $this;
    }

    // }}}

    // {{{ Structure info

    // }}}

    // {{{ Operations

    /**
     * Applies the callback to the elements of the given arrays
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function each($callback)
    {
        foreach ($this as $key => $value) {
            $callback($value, $key, $this);
        }

        return $this;
    }

    /**
     * Applies the callback to the elements of the given arrays
     * 
     * @param callable $callback Callback
     *  
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function map($callback)
    {
        foreach ($this as $key => $value) {
            $this->offsetSet($key, $callback($value, $key, $this));
        }

        return $this;
    }

    /**
     * Check - each element must be tested
     * 
     * @param callable $callback Callback
     *  
     * @return boolean
     * @since  1.0.0
     */
    public function every($callback)
    {
        $result = false;
        foreach ($this as $key => $value) {
            if (!$callback($value, $key, $this)) {
                $result = false;
                break;

            } else {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Check - some element must be tested
     * 
     * @param callable $callback Callback
     *  
     * @return boolean
     * @since  1.0.0
     */
    public function some($callback)
    {
        $result = false;
        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check - none element must be tested
     *
     * @param callable $callback Callback
     *
     * @return boolean
     * @since  1.0.0
     */
    public function none($callback)
    {
        $result = false;
        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this)) {
                $result = false;
                break;

            } else {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Invoke all elements
     * 
     * @param string $methodName Method name
     * @param array  $arguments  Method arguments OPTIONAL
     *  
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function invoke($methodName, array $arguments = array())
    {
        foreach ($this as $value) {
            call_user_func_array(array($value, $methodName), $arguments);
        }

        return $this;
    }

    /**
     * Fetch a single property from a collection of objects
     * 
     * @param string $propertyName Property name
     *  
     * @return array
     * @since  1.0.0
     */
    public function pluck($propertyName)
    {
        $list = array();

        foreach ($this as $key => $value) {
            $list[$key] = $value->$propertyName;
        }

        return $list;
    }

    /**
     * Splits a collection into parts by callback
     *
     * @param callable $callback Callback
     * @param integer  $limit    Parts limi OPTIONAL
     *
     * @return array
     * @since  1.0.0
     */
    public function explode($callback, $limit = null)
    {
        $parts = array();
        $index = 0;

        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this) && isset($limit) && $index < $limit) {
                $index++;
            }
            $list[$index] = $value;
        }

        return $list;
    }

   /**
     * Splits a collection into groups by the key returned by the callback
     *
     * @param callable $callback Callback
     *
     * @return array
     * @since  1.0.0
     */
    public function group($callback)
    {
        $groups = array();

        foreach ($this as $key => $value) {
            $index = $callback($value, $key, $this);

            if (!isset($groups[$index])) {
                $groups[$index] = array();
            }

            $groups[$index][$key] = $value;
        }

        return $groups;
    }

    /**
     * Splits a collection into two by callback. Thruthy values come first
     *
     * @param callable $callback Callback
     *
     * @return array
     * @since  1.0.0
     */
    public function partition($callback)
    {
        $valid = array();
        $invalid = array();

        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this)) {
                $valid[$key] = $value;

            } else {
                $invalid[$key] = $value;
            }
        }

        return array($valid, $invalid);
    }

    /**
     * Applies a callback to each element in the collection and reduces the collection to a single scalar value
     * Starts with the first element in the collection
     *
     * @param callable $callback Callback
     * @param mixed    $inital   Reduction initial value
     *
     * @return mixed
     * @since  1.0.0
     */
    public function reduceLeft($callback, $initial = null)
    {
        $reduction = $initial;
        foreach ($this as $value) {
            $reduction = $callback($value, $key, $this, $reduction);
        }

        return $reduction;
    }

    /**
     * Applies a callback to each element in the collection and reduces the collection to a single scalar value
     * Starts with the last element in the collection
     *
     * @param callable $callback Callback
     * @param mixed    $inital   Reduction initial value
     *
     * @return mixed
     * @since  1.0.0
     */
    public function reduceRight($callback, $initial = null)
    {
        $reduction = $initial;

        do {

            $value = $this->pop();
            if (isset($value)) {
                $reduction = $callback($value, $key, $this, $reduction);
            }

        } while(isset($value));

        return $reduction;
    }

    /**
     * Returns the first element of the collection where the callback returned true
     *
     * @param callable $callback Callback
     *
     * @return mixed
     * @since  1.0.0
     */
    public function first($callback = null)
    {
        $result = null;

        foreach ($this as $value) {
            if (!$callback || $callback($value, $key, $this)) {
                $result = $value;
                break;
            }
        }

        return $value;
    }

    /**
     * Returns the last element of the collection where the callback returned true
     *
     * @param callable $callback Callback
     *
     * @return mixed
     * @since  1.0.0
     */
    public function last($callback = null)
    {
        $result = null;

        foreach ($this as $value) {
            if (!$callback || $callback($value, $key, $this)) {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * Calculates the product of all elements 
     * 
     * @return float
     * @since  1.0.0
     */
    public function product()
    {
        return array_product($this->getArrayCopy());
    }

    /**
     * Calculates the ratio of all elements
     * 
     * @return float
     * @since  1.0.0
     */
    public function ratio()
    {
        $result = null;
        foreach ($this as $value) {
            if (isset($result)) {
                $result = $result / $value;

            } else {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * Calculates the sum of all elements
     * 
     * @return float
     * @since  1.0.0
     */
    public function sum()
    {
        return array_sum($this->getArrayCopy());
    }

    /**
     * Calculates the difference of all elements 
     * 
     * @return float
     * @since  1.0.0
     */
    public function difference()
    {
        $result = null;
        foreach ($this as $value) {
            if (isset($result)) {
                $result = $result - $value;

            } else {
                $result = $value;
            }
        }

        return $result;
    }

    // }}}

     // {{{ Callback-based structure changes

    /**
     * Keep only those items that have been tested
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function select($callback)
    {
        foreach ($this as $key => $value) {
            if (!$callback($value, $key, $this)) {
                $this->offsetUnset($key);
            }
        }

        return $this;
    }

    /**
     * Keep only those items that have not been tested
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function reject($callback)
    {
        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this)) {
                $this->offsetUnset($key);
            }
        }

        return $this;
    }

    /**
     * Keep only those elements that go after the triggering callback
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function dropFirst($callback)
    {
        $found = false;
        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this)) {
                $found = true;
            }

            if (!$found) {
                $this->offsetUnset($key);
            }
        }

        return $this;
    }

    /**
     * Keep only those elements that go before the triggering callback
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\ArrayObject
     * @since  1.0.0
     */
    public function dropLast($callback)
    {
        $found = false;
        foreach ($this as $key => $value) {
            if ($callback($value, $key, $this)) {
                $found = true;
            }

            if ($found) {
                $this->offsetUnset($key);
            }
        }

        return $this;
    }

    // }}}
}
