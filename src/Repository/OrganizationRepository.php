<?php

namespace App\Repository;

use App\Entity\Organization;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

class OrganizationRepository extends CustomRepository
{
    public function getAll(Request $request): array
    {   
        $query = $request->query;
        $searchParam = $query->get("search", "");
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("o.id, o.name, SUM(r.peoples) AS peoples")
            ->from(Organization::class, "o")
            ->leftJoin("o.buildings", "b")
            ->leftJoin("b.rooms", "r")
            ->groupBy("o.id")
            ->where("o.name LIKE :search")
            ->setParameter("search", "%$searchParam%")
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
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("o.id, o.name, SUM(r.peoples) AS peoples")
            ->from(Organization::class, "o")
            ->join("o.buildings", "b")
            ->join("b.rooms", "r")
            ->groupBy("o.id")
            ->where("o.id = :id")
            ->setParameter("id", $id->toBinary());
        
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
    
    public function getBuildingsOfOrganizationById(Request $request, Ulid $id)
    {
        $query = $request->query;
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("o.id AS organization_id, o.name AS organization_name, b.id, b.name, b.zipcode, b.id, SUM(r.peoples) AS peoples")
            ->from(Organization::class, "o")
            ->innerJoin("o.buildings", "b")
            ->join("b.rooms", "r")
            ->groupBy("b.id")
            ->where("o.id = :id")
            ->setParameter("id", $id->toBinary())
            ->setFirstResult($query->get("offset", "0"))
            ->setMaxResults($query->get("limit", "50"));
            
        $paginator = new Paginator($queryBuilder);
        
        return[
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ];
    }
    
}
