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

class Closure
{
    /**
     * Callback 
     * 
     * @var   callable
     * @since 1.0.0
     */
    protected $callback;

    /**
     * Initial callback 
     * 
     * @var   callable
     * @since 1.0.0
     */
    protected $initialCallback;

    /**
     * Get callable object as normal callable construction
     * 
     * @param mixed $callable Callable
     *  
     * @return callable
     * @since  1.0.0
     * @throws \InvalidArgumentException
     */
    public static function getAsCallable($callable)
    {
        if (!is_callable($callable)) {
            if ($callable instanceof static) {
                $callable = $callable->getClosure();

            } else {
                throw new \InvalidArgumentException('$callable arguments is not callable');
            }
        }

        return $callable;
    }

    /**
     * Constructor
     * 
     * @param callable|\PHPF\Closure $callback Callback
     *  
     * @return void
     * @since  1.0.0
     * @throws \InvalidArgumentException
     */
    public function __construct($callback)
    {
        if (is_object($callback) && $callback instanceOf static) {
            $callback = $callback->getClosure();
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback is not callback type.');
        }

        $this->callback = $callback;
        $this->initialCallback = $callback;
    }

    // {{{ Factories

    /**
     * Build instance
     * 
     * @param callable $closure Closure
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public static function build($closure)
    {
        return new static($closure);
    }

    /**
     * Build as closures concatinations 
     * 
     * @param callable|\PHPF\Closure $closure1 Closure 1
     * @param callable|\PHPF\Closure $closure2 Closure 2
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     * @throws \InvalidArgumentException
     */
    public static function buildByConcat($closure1, $closure2)
    {
        $closures = static::prepareClosuresList(func_get_args());

        if (2 > count($closures)) {
            throw new \InvalidArgumentException('Closures list must be length 2 or bigger');
        }

        $func = function() use ($closures) {
            $args = func_get_args();
            foreach ($closures as $closure) {
                $args = call_user_func_array($closure, $args);
            }

            return $args;
        };

        return new static($func);
    }

    // }}}

    // {{{ Getters

    /**
     * Get callable 
     * 
     * @return callable
     * @since  1.0.0
     */
    public function getCallable()
    {
        return $this->callback;
    }

    /**
     * Get closure 
     * 
     * @return \Closure
     * @since  1.0.0
     */
    public function getClosure()
    {
        if (is_array($this->callback) || is_string($this->callback)) {
            $callable = $this->callback;
            $callback = function() use ($callable) {
                return call_user_func_array($callable, func_get_args());
            };

        } else {
            $callback = $this->callback;
        }

        return $callback;
    }

    /**
     * Get reflection object
     * 
     * @return \ReflectionMethod|\ReflectionFunction
     * @since  1.0.0
     */
    public function getReflection()
    {
        if (is_array($this->initialCallback)) {
            $reflection = new \ReflectionMethod($this->initialCallback[0], $this->initialCallback[1]);

        } elseif (is_string($this->initialCallback) && false !== strpos($this->initialCallback, '::')) {

            list($class, $method) = explode('::', $this->initialCallback, 2);
            $reflection = new \ReflectionMethod($class, $method);

        } else {
            $reflection = new \ReflectionFunction($this->initialCallback);
        }

        return $reflection;
    }

    // }}}

    // {{{ Modifications

    /**
     * Flip closure arguments list
     * 
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function flip()
    {
        $callback = $this->callback;
        $this->callback = function () use ($callback) {
            return call_user_func_array($callback, array_reverse(func_get_args()));
        };

        return $this;
    }

    /**
     * Assign default arguments for closure
     *
     * @param mixed $argument First argument
     * 
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function assignDefaultArgs($argument)
    {
        $args = func_get_args();
        $callback = $this->callback;
        $this->callback = function () use ($callback, $args) {
            return call_user_func_array($callback, func_get_args() + $args);
        };

        return $this;
    }

    /**
     * Assign arguments for closure
     *
     * @param mixed $argument First argument
     *
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function assignArgs($argument)
    {
        $args = func_get_args();
        $callback = $this->callback;
        $this->callback = function () use ($callback, $args) {
            return call_user_func_array($callback, $args + func_get_args());
        };

        return $this;
    }

    /**
     * Concatination any closure(s)
     * 
     * @param callable|\PHPF\Closure $closure Closure
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     * @throws \InvalidArgumentException
     */
    public function concat($closure)
    {
        $closures = static::prepareClosuresList(func_get_args());

        if (1 > count($closures)) {
            throw new \InvalidArgumentException('Closures list must be length 1 or bigger');
        }

        array_unshift($closures, $this->callback);

        $this->callback = function() use ($closures) {
            $args = func_get_args();
            foreach ($closures as $closure) {
                $args = call_user_func_array($closure, $args);
            }

            return $args;
        };

        return $this;
    }

    // }}}

    // {{{ Operations

    /**
     * Fork (clone) current object
     * 
     * @return \PHPF\CLosure
     * @since  1.0.0
     */
    public function fork()
    {
        return clone $this;
    }

    /**
     * Clone current object into reference argument
     * 
     * @param mixed &$clone Clone reference
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function cloneOuter(&$clone)
    {
        $clone = clone $this;

        return $this;
    }

    // }}}

    // {{{ Call

    /**
     * Call 
     * 
     * @return mixed
     * @since  1.0.0
     */
    public function call()
    {
        return call_user_func_array($this->callback, func_get_args());
    }

    /**
     * Call closure with arguments list
     * 
     * @param array $arguments Arguments list
     *  
     * @return mixed
     * @since  1.0.0
     */
    public function callArray(array $arguments = array())
    {
        return call_user_func_array($this->callback, $arguments);
    }
 
    // }}}

    // {{{ Service static methods

    /**
     * Prepare closures list 
     * 
     * @param array $args Initial list
     *  
     * @return array
     * @since  1.0.0
     */
    protected static function prepareClosuresList(array $args)
    {
        $closures = array();
        foreach ($args as $arg) {
            if (is_callable($arg)) {
                $closures[] = $arg;

            } elseif (is_object($arg) && arg instanceOf static) {
                $closures[] = $arg->getClosure();
            }
        }

        return $closures;
    }

    // }}}
}

