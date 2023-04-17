<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

class CustomRepository extends ServiceEntityRepository {
    
    protected $entityName;
    
    public function __construct(ManagerRegistry $registry) {
        $this->entityName = preg_replace(
            ["/^App\\\Repository/", "/Repository$/"],
            ["App\\Entity"],
            get_class($this)
        );
        
        parent::__construct($registry, $this->entityName);
    }
    
    public function getAll(Request $request): array
    {
        
        $query = $request->query;
        $search = $query->get("search", "");
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("e")
            ->from($this->entityName, "e")
            ->where("e.name LIKE :search")
            ->setParameter("search", "%$search%")
            ->setFirstResult($query->get("offset", "0"))
            ->setMaxResults($query->get("limit", "50"));
        
        $paginator = new Paginator($queryBuilder);
        
        return [
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ];
    }
    
    public function getById(Ulid $id)
    {
        return parent::find($id->toBinary());
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