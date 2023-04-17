<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Room;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

class BuildingRepository extends CustomRepository
{
    
    public function getAll(Request $request): array
    {
        $query = $request->query;
        $search = $query->get("search", "");
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("b.id, b.name, b.zipcode, o.id as organization_id, o.name as organization_name, SUM(r.peoples) AS peoples")
            ->from(Building::class, "b")
            ->join("b.rooms", "r")
            ->join("b.organization", "o")
            ->groupBy("b.id")
            ->where("b.name LIKE :search")
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
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("b.id, b.name, b.zipcode, o.id as organization_id, o.name as organization_name, SUM(r.peoples) AS peoples")
            ->from(Building::class, "b")
            ->join("b.rooms", "r")
            ->join("b.organization", "o")
            ->where("b.id = :id")
            ->groupBy("b.id")
            ->setParameter("id", $id->toBinary());
            
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
    
    public function getRoomsOfBuildingById(Request $request, Ulid $id)
    {
        $query = $request->query;
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('r')
            ->from(Room::class, 'r')
            ->where("r.building = :id")
            ->setParameter("id", $id->toBinary())
            ->setFirstResult($query->get("offset", "0"))
            ->setMaxResults($query->get("limit", "50"));
            
        $paginator = new Paginator($queryBuilder);
        
        return [
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ];
    }
    
}
