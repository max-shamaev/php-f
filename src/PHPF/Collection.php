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
 * Collection
 * 
 * @since 1.0.0
 */
class Collection extends APHPF implements \ArrayAccess, \Serializable, \Countable, \IteratorAggregate
{
    /**
     * Data 
     * 
     * @var   array
     * @since 1.0.0
     */
    protected $data = array();

    /**
     * Constructor
     * 
     * @param array|\PHPF\Collection|\Collection $data Data OPTIONAL
     *  
     * @return void
     * @since  1.0.0
     */
    public function __construct($data = array())
    {
        if (is_object($data)) {

            if ($data instanceof static || method_exists($data, 'toArray')) {
                $data = $data->toArray();

            } elseif ($data instanceof \Collection) {
                $data = $data->getArrayCopy();

            } else {
                throw \InvalidArgumentException('$data object can not convert to array');
            }

        } elseif (!is_array($data)) {
            throw \InvalidArgumentException('$data is not array type');
        }

        $this->data = $data;
    }

    // {{{ Factories

    /**
     * Build instance
     *
     * @param array|\PHPF\Collection|\Collection $data Data OPTIONAL
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public static function build($data = array())
    {
        return new static($data);
    }

    /**
     * Build as tree 
     * 
     * @param array $data Data
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public static function buildTree(array $data = array())
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = static::buildTree($v);
            }
        }

        return new static($data);
    }

    /**
     * Combine array from keys list and values list
     * 
     * @param mixed $keys   Keys
     * @param mixed $values Values
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public static function combine($keys, $values)
    {
        return new static(array_combine(static::getAsArray($keys), static::getAsArray($values)));
    }

    /**
     * Build by array difference. Computes the difference of arrays with additional index check
     * 
     * @param array $array1 First array
     * @param array $array2 Second array
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public static function buildByDiffAssoc(array $array1, array $array2)
    {
        return new static(call_user_func_array('array_diff_assoc', static::getAsArrays(func_get_args())));
    }

    // }}}

    // {{{ Helpers

    /**
     * Get data as array 
     * 
     * @param mixed $data Data
     *  
     * @return array
     * @since  1.0.0
     */
    protected static function getAsArray($data)
    {
        if (is_object($data)) {

            if ($data instanceof static || method_exists($data, 'toArray')) {
                $data = $data->toArray();

            } elseif ($data instanceof \Collection) {
                $data = $data->getArrayCopy();

            } else {
                throw \InvalidArgumentException('$data object can not convert to array');
            }

        } elseif (!is_array($data)) {
            throw \InvalidArgumentException('$data is not array type');
        }

        return $data;
    }

    /**
     * Get mixed list as arrays list
     * 
     * @param array $arrays Mixed objects
     *  
     * @return array
     * @since  1.0.0
     */
    protected static function getAsArrays(array $arrays)
    {
        foreach ($arrays as $k => $v) {
            $arrays[$k] = static::getAsArray($v);
        }

        return $arrays;
    }

    // }}}

    // {{{ Property access

    /**
     * Getter
     * 
     * @param mixed $key Cell key
     *  
     * @return mixed
     * @since  1.0.0
     */
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Setter
     * 
     * @param mixed $key   Cell key
     * @param mixed $value New value
     *  
     * @return void
     * @since  1.0.0
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Check cell availability
     * 
     * @param mixed $key Cell key
     *  
     * @return boolean
     * @since  1.0.0
     */
    public function __isset($key)
    {
        return array_key_exists($lkey, $this->data);
    }

    /**
     * Unset cell
     * 
     * @param mixed $key Cell key
     *  
     * @return void
     * @since  1.0.0
     */
    public function __unset($key)
    {
        if (array_key_exists($lkey, $this->data)) {
            unset($this->data[$key]);
        }
    }

    // }}}

    // {{{ ArrayAccess

    /**
     * Check cell availability
     *
     * @param mixed $key Cell key
     *
     * @return boolean
     * @since  1.0.0
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Getter
     *
     * @param mixed $key Cell key
     *
     * @return mixed
     * @since  1.0.0
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Setter
     *
     * @param mixed $key   Cell key
     * @param mixed $value New value
     *
     * @return void
     * @since  1.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * Unset cell
     *
     * @param mixed $key Cell key
     *
     * @return void
     * @since  1.0.0
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }


    // }}}

    // {{{ Serializable

    /**
     * Serialize data
     * 
     * @return string
     * @since  1.0.0
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Unserialize 
     * 
     * @param string $serialized Serialized data
     *  
     * @return void
     * @since  1.0.0
     */
    public function unserialize($serialized)
    {
        $this->Data = unserialize($serialized);
    }

    // }}}

    // {{{ Countable

    /**
     * Count 
     * 
     * @return integer
     * @since  1.0.0
     */
    public function count()
    {
        return count($this->data);
    }

    // }}}

    // {{{ IteratorAggregate

    /**
     * Get iterator 
     * 
     * @return \ArrayIterator
     * @since  1.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    // }}}

    // {{{ Structure changes

    /**
     * Set cell
     * 
     * @param mixed $key   Key
     * @param mixed $value Value
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Append value
     * 
     * @param mixed $value New value
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function push($value)
    {
        $this->data[] = $value;

        return $this;
    }

    /**
     * Unshift (add first value)
     * 
     * @param mixed $value Value
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function unshift($value)
    {
        array_unshift($this->data, $value);

        return $this;
    }

    /**
     * Pop last cell
     *
     * @param boolean $returnValue Return value or not OPTIONAL
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function pop($returnValue = false)
    {
        $value = array_pop($this->data);

        return $returnValue ? $value : $this;
    }

    /**
     * Shift first element
     *
     * @param boolean $returnValue Return value or not OPTIONAL
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function shift($returnValue = false)
    {
        $value = array_shift($this->data);

        return $returnValue ? $value : $this;
    }

    /**
     * Clear data
     * 
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function clear()
    {
        $this->data;

        return $this;
    }

    /**
     * Remove a portion of the array and replace it with something else
     * 
     * @param integer $offset      Offset
     * @param integer $length      Splice length OPTIONAL
     * @param mixed   $replacement Replacement part OPTIONAL
     *  
     * @return array|\PHPF\Collection
     * @since  1.0.0
     */
    public function splice($offset, $length = 0, $replacement = null)
    {
        if (2 > func_num_args()) {
            array_splice($this->data, $offset, $length, $replacement);

        } else {
            array_splice($this->data, $offset, $length);
        }

        return $this;
    }

    /**
     * Extract a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on.
     * 
     * @param integer $offset Offset
     * @param integer $length Range length OPTIONAL
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function slice($offset, $length = null)
    {
        $this->data = array_slice($this->data, $offset, $length, true);

        return $this;
    }

    /**
     * Merge current array ant some arrays from arguments
     * 
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function merge()
    {
        $args = func_get_args();
        array_unshift($args, $this->data);
        $this->data = call_user_func_array('array_merge', $args);

        return $this; 
    }

    /**
     * Remove cell by key 
     * 
     * @param mixed $key Key
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function removeKey($key)
    {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Remove cell by value
     * 
     * @param mixed $value Value
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function removeElement($value)
    {
        $key = array_search($element, $this->data, true);
        if (false !== $key) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Computes the difference of arrays with additional index check
     * 
     * @param mixed $array1 Another array
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function diffAssoc($array1)
    {
        return $this->combineCallback('array_diff_assoc', func_get_args());
    }

    /**
     * Computes the difference of arrays using keys for comparison
     *
     * @param mixed $array1 Another array
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function diffKey($array1)
    {
        return $this->combineCallback('array_diff_key', func_get_args());
    }

    /**
     * Computes the difference of arrays with additional index check which is performed by a user supplied callback function
     * 
     * @param mixed $array1 First array
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function diffUassoc($array1)
    {
        $args = func_get_args();
        $args[] = \PHP\Closure::getAsCallable(array_pop($args));

        return $this->combineCallback('array_diff_uassoc', $args);
    }

    /**
     * Combine-specified callback 
     * 
     * @param string $callback Function name
     * @param array  $args     Arguments
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    protected function combineCallback($callback, array $args)
    {
        $this->data = call_user_func_array(
            $callback,
            array_merge(array($this->data), static::getAsArrays($args))
        );

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

    // {{{ Information and property

    /**
     * Counts all the values of an array
     * 
     * @return array
     * @since  1.0.0
     */
    public function countValues()
    {
        return array_count_values($this->data);
    }

    /**
     * Check contains value
     * 
     * @param mixed $value Value
     *  
     * @return boolean
     * @since  1.0.0
     */
    public function contains($value)
    {
        return in_array($value, $this->_elements, true);
    }

    /**
     * Tests for the existance of an element that satisfies the given predicate
     * 
     * @param callable|\PHPF\Closure $callback Callback
     *  
     * @return boolean
     * @since  1.0.0
     */
    public function exists($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $result = false;

        foreach ($this->data as $key => $element) {
            if ($callback($key, $element)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Applies the given predicate p to all elements of this collection,
     * returning true, if the predicate yields true for all elements.
     * 
     * @param callable $callback Callback
     *  
     * @return boolean
     * @since  1.0.0
     */
    public function forAll($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $result = true;
        foreach ($this->data as $key => $value) {
            if (!$callback($value, $key, $this)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Get index of value
     * 
     * @param mixed $value Value
     *  
     * @return mixed
     * @since  1.0.0
     */
    public function indexOf($value)
    {
        return array_search($value, $this->data, true);
    }

    /**
     * Get keys 
     * 
     * @return array
     * @since  1.0.0
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * Get values as plain array
     * 
     * @return array
     * @since  1.0.0
     */
    public function getValues()
    {
        return array_values($this->data);
    }

    // }}}

    // {{{ Getters

    /**
     * Get value
     * 
     * @param mixed $key Key
     *  
     * @return mixed
     * @since  1.0.0
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Get first cell
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function first()
    {
        return reset($this->data);
    }

    /**
     * Get last cell
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function last()
    {
        return end($this->data);
    }

    /**
     * Get current cell key 
     * 
     * @return void
     * @since  1.0.0
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Move the internal cursor to the next element
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * Get current cell
     * 
     * @return void
     * @since  1.0.0
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Remove cell by key and return removed value
     * 
     * @param mixed $key Cell key
     *  
     * @return mixed
     * @since  1.0.0
     */
    public function removeAndReturn($key)
    {
        $removed = null;

        if (array_key_exists($key, $this->data)) {
            $removed = $this->data[$key];
            unset($this->data[$key]);
        }

        return $removed;
    }

    // }}}

    // {{{ Operations

    /**
     * Applies the callback to the elements of the given arrays
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function each($callback)
    {
        foreach ($this->data as $key => $value) {
            $callback($value, $key, $this);
        }

        return $this;
    }

    /**
     * Applies the callback to the elements of the given arrays
     * 
     * @param callable $callback Callback
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function map($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        foreach ($this->data as $key => $value) {
            $this->data[$key] = $callback($value, $key, $this);
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
        $callback = \PHPF\Closure::getAsCallable($callback);

        $result = false;
        foreach ($this->data as $key => $value) {
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
        $callback = \PHPF\Closure::getAsCallable($callback);

        $result = false;
        foreach ($this->data as $key => $value) {
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
        $callback = \PHPF\Closure::getAsCallable($callback);

        $result = false;
        foreach ($this->data as $key => $value) {
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
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function invoke($methodName, array $arguments = array())
    {
        foreach ($this->data as $value) {
            call_user_func_array(array($value, $methodName), $arguments);
        }

        return $this;
    }

    /**
     * Fetch a single property from a collection of objects
     * 
     * @param string $propertyName Property name
     *  
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function pluck($propertyName)
    {
        $list = array();

        foreach ($this->data as $key => $value) {
            $list[$key] = $value->$propertyName;
        }

        $this->data = $list;

        return $this;
    }

    /**
     * Splits a collection into parts by callback
     *
     * @param callable $callback Callback
     * @param integer  $limit    Parts limit OPTIONAL
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function explode($callback, $limit = null)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $parts = array();
        $index = 0;

        foreach ($this->data as $key => $value) {
            if ($callback($value, $key, $this) && isset($limit) && $index < $limit) {
                $index++;
            }
            $list[$index] = $value;
        }

        $this->data = $list;

        return $this;
    }

   /**
     * Splits a collection into groups by the key returned by the callback
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function group($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $groups = array();

        foreach ($this->data as $key => $value) {
            $index = $callback($value, $key, $this);

            if (!isset($groups[$index])) {
                $groups[$index] = array();
            }

            $groups[$index][$key] = $value;
        }

        $this->data = $groups;

        return $this;
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
        $callback = \PHPF\Closure::getAsCallable($callback);

        $valid = array();
        $invalid = array();

        foreach ($this->data as $key => $value) {
            if ($callback($value, $key, $this)) {
                $valid[$key] = $value;

            } else {
                $invalid[$key] = $value;
            }
        }

        return array(new static($valid), new static($invalid));
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
        $callback = \PHPF\Closure::getAsCallable($callback);

        $reduction = $initial;
        foreach ($this->data as $value) {
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
        $callback = \PHPF\Closure::getAsCallable($callback);

        $reduction = $initial;

        $reduction = $initial;
        foreach (array_reverse($this->data, true) as $value) {
            $reduction = $callback($value, $key, $this, $reduction);
        }

        return $reduction;
    }

    /**
     * Calculates the product of all elements 
     * 
     * @return float
     * @since  1.0.0
     */
    public function product()
    {
        return array_product($this->data());
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
        foreach ($this->data as $value) {
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
        return array_sum($this->data);
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
        foreach ($this->data as $value) {
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
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function select($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $this->data = array_filter($this->data, $callback);

        return $this;
    }

    /**
     * Keep only those items that have not been tested
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function reject($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        foreach ($this->data as $key => $value) {
            if ($callback($value, $key, $this)) {
                unset($this->data[$key]);
            }
        }

        return $this;
    }

    /**
     * Keep only those elements that go after the triggering callback
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function dropFirst($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $found = false;
        foreach ($this->data as $key => $value) {
            if ($callback($value, $key, $this)) {
                $found = true;
            }

            if (!$found) {
                unset($this->data[$key]);
            }
        }

        return $this;
    }

    /**
     * Keep only those elements that go before the triggering callback
     *
     * @param callable $callback Callback
     *
     * @return \PHPF\Collection
     * @since  1.0.0
     */
    public function dropLast($callback)
    {
        $callback = \PHPF\Closure::getAsCallable($callback);

        $found = false;
        foreach ($this->data as $key => $value) {
            if ($callback($value, $key, $this)) {
                $found = true;
            }

            if ($found) {
                unset($this->data[$key]);
            }
        }

        return $this;
    }

    // }}}
}
