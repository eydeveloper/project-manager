<?php

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\User\Entity\User\Email;

class Command
{
    public Email $email;
}
