<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class NetworkView
{
    use FromArrayTrait;

    public string $network;
    public string $identity;
}
