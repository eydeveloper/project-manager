<?php

namespace App\Model\User\UseCase\Reset;

use App\Model\User\Entity\User\Email;

class Command
{
    public Email $email;
}
