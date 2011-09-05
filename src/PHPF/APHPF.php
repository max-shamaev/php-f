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
 * Abstract base class of PHPF package
 * 
 * @since 1.0.0
 */
abstract class APHPF
{
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

}

