<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\Traits\FromArrayTrait;

class DetailView
{
    use FromArrayTrait;

    /**
     * @var string|null
     */
    public ?string $id = null;

    /**
     * @var string|null
     */
    public ?string $date = null;

    /**
     * @var string|null
     */
    public ?string $email = null;

    /**
     * @var string|null
     */
    public ?string $role = null;

    /**
     * @var string|null
     */
    public ?string $status = null;

    /**
     * @var DetailView[]|null
     */
    public ?array $networks = null;
}
