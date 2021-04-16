<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class NetworkView
{
    use FromArrayTrait;

    /**
     * @var string|null
     */
    public ?string $network = null;

    /**
     * @var string|null
     */
    public ?string $identity = null;
}
