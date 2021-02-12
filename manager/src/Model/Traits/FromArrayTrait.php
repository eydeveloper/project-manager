<?php

declare(strict_types=1);

namespace App\Model\Traits;

use App\ReadModel\User\AuthView;
use JetBrains\PhpStorm\Pure;

trait FromArrayTrait
{
    #[Pure]
    public static function fromArray(array $data = []): mixed
    {
        foreach (get_object_vars($object = new self) as $property => $default) {
            $object->$property = $data[$property] ?? $default;
        }

        return $object;
    }
}
