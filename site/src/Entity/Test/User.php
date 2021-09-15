<?php

namespace App\Entity\Test;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Test\Base\BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Test\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
    }
}