<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class DetailView
{
    use FromArrayTrait;

    public ?string $id = null;
    public ?string $date = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;
    public ?NetworkView $networks = null;
}
