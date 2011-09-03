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
     */
    public static function getAsCallable($callable)
    {
        if (!is_callable($callable)) {
            if ($callable instanceof static) {
                $callable = $callable->getClosure();

            } else {
                throw \InvalidArgumentException('$callable arguments is not callable');
            }
        }

        return $callable;
    }

    /**
     * Constructor
     * 
     * @param callable $callback Callback
     *  
     * @return void
     * @since  1.0.0
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw \InvalidArgumentException('$callback is not callback type.');
        }

        $this->callback = $callback;
        $this->initialCallback = $callback;
    }

    // {{{ Fabrics

    /**
     * Build by class or object
     * 
     * @param string $class  Class name
     * @param string $method Method name
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public static function buildByClass($class, $method)
    {
        return new static($class . '::' . $method);
    }

    /**
     * Build by object and method name
     * 
     * @param object $object Object
     * @param string $method Method name
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public static function buildByObject($object, $method)
    {
        return new static(array($object, $method));
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
            throw \InvalidArgumentException('Closures list must be length 2 or bigger');
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
        return is_array($this->initialCallback)
            ? new \ReflectionMethod($this->initialCallback[0], $this->initialCallback[1])
            : new \ReflectionFunction($this->initialCallback);
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
            return call_user_func_array($callback, array_flip(func_get_args()));
        };

        return $this;
    }

    /**
     * Assign default arguments for closure
     * 
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function assignDefaultArgs()
    {
        $args = func_get_args();
        $callback = $this->callback;
        $this->callback = function () use ($callback, $args) {
            return call_user_func_array($callback, array_merge($args, func_get_args()));
        };

        return $this;
    }

    /**
     * Assign arguments for closure
     *
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function assignArgs()
    {
        $args = func_get_args();
        $callback = $this->callback;
        $this->callback = function () use ($callback, $args) {
            return call_user_func_array($callback, $args + func_get_args());
        };

        return $this;
    }

    /**
     * Compose - current closure is wrapped specidied wrapper-closure
     * 
     * @param callable|\PHPF\Closure $wrapper Wrapper
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function compose($wrapper)
    {
        if ($wrapper instanceof static) {
            $wrapper = $wrapper->getClosure();

        } elseif (!is_callable($wrapper)) {
            throw \InvalidArgumentException('$wrapper is not callback type.');
        }

        $callback = $this->callback;
        $this->callback = function () use ($callback, $wrapper) {
            return call_user_func($wrapper, call_user_func_array($callback, func_get_args()));
        };

        return $this;
    }

    /**
     * Wrap current closure around specified wrapper
     * 
     * @param callable|\PHPF\Closure $wrapAround Wrapper
     *  
     * @return \PHPF\Closure
     * @since  1.0.0
     */
    public function wrap($wrapAround)
    {
        if ($wrapAround instanceof static) {
            $wrapAround = $wrapAround->getClosure();

        } elseif (!is_callable($wrapAround)) {
            throw \InvalidArgumentException('$wrapAround is not callback type.');
        }

        $callback = $this->callback;
        $this->callback = function () use ($callback, $wrapAround) {
            return call_user_func($callback, call_user_func_array($wrapAround, func_get_args()));
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
     */
    public function concat($closure)
    {
        $closures = static::func_get_args(func_get_args());

        if (1 > count($closures)) {
            throw \InvalidArgumentException('Closures list must be length 1 or bigger');
        }

        $closures = array_shift($closures, $this->callback);

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

    // {{{ Information and properties

    /**
     * Count arguments
     * 
     * @return integer
     * @since  1.0.0
     */
    public function countArgs()
    {
        return $this->getReflection()->getNumberOfParameters();
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
        return call_user_func_array($this->getClosure(), func_get_args());
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

