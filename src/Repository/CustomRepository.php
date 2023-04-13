<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomRepository extends ServiceEntityRepository {
  
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, preg_replace(
            ['/^App\\\Repository/', "/Repository$/"],
            ["App\\Entity"],
            get_class($this)
        ));
    }
    
    public function save($entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function remove($entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
  
}