<?php

namespace App\Traits;

trait LoadData
{
     public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }
}
