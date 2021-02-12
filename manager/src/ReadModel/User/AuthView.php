<?php

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class AuthView
{
    use FromArrayTrait;

    public ?string $id = null;
    public ?string $email = null;
    public ?string $password_hash = null;
    public ?string $role = null;
}
