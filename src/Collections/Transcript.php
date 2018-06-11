<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;

class Transcript extends Collection
{
    public function __construct(array $models = [])
    {
        return parent::__construct($models);
    }
}
