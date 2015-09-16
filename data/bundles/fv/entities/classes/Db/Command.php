<?php

/**
 * Created by cah4a.
 * Time: 16:43
 * Date: 28.05.14
 */
abstract class Db_Command
{

    abstract function getSql();

    function __toString()
    {
        return $this->getSql();
    }

} 