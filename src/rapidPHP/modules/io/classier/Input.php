<?php

namespace rapidPHP\modules\io\classier;

interface Input
{

    /**
     * get params
     * @param $name
     * @return mixed
     */
    public function get($name = null);

}