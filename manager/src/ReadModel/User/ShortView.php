<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class ShortView
{
    use FromArrayTrait;

    public ?string $id = null;
    public ?string $email = null;
    public ?string $status = null;
    public ?string $role = null;
}
