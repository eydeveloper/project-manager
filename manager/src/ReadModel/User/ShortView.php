<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Trait\FromArrayTrait;

class ShortView
{
    use FromArrayTrait;

    /**
     * @var string|null
     */
    public ?string $id = null;

    /**
     * @var string|null
     */
    public ?string $email = null;

    /**
     * @var string|null
     */
    public ?string $status = null;

    /**
     * @var string|null
     */
    public ?string $role = null;
}
