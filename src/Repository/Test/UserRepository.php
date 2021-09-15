<?php
namespace App\Repository\Test;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getThemLimited($number = 15)
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->setMaxResults($number);
        return $queryBuilder->getQuery()->getResult();
    }
}