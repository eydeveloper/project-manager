<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class NetworkView
{
    use FromArrayTrait;

    /**
     * @var string
     */
    public string $network;

    /**
     * @var string
     */
    public string $identity;
}
