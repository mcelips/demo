<?php

namespace App\Services;

/**
 * Trait to supplement the PHP 5.5 function ::class
 *
 */
trait ClassName
{
    /**
     * Returns the Full name of a class (equivalent of ::class which can be used starting from php 5.5)
     *
     * @return string the class name including namespace
     */
    static public function className()
    {
        return get_called_class();
    }
}
