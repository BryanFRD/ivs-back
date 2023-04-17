<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Ulid;

#[Route(path: "/organization")]
class OrganizationController extends CustomController
{

    #[Route(
        path: "",
        name: "organization_show_all",
        methods: ["GET"]
    )]
    public function getAllOrganization(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $query = $request->query;
        $searchParam = $query->get("search", "");
        
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select("o.id, o.name, SUM(r.peoples) AS peoples")
            ->from(Organization::class, "o")
            ->join("o.buildings", "b")
            ->join("b.rooms", "r")
            ->groupBy("o.id")
            ->where("o.name LIKE :search")
            ->setParameter("search", "%$searchParam%")
            ->setFirstResult($query->get("offset", "0"))
            ->setMaxResults($query->get("limit", "50"));
            
        $paginator = new Paginator($queryBuilder);
        
        return new JsonResponse([
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ]);
    }

    #[Route(
        path: "/{id}",
        name: "organization_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getOrganizationById(EntityManagerInterface $entityManager, Ulid $id): JsonResponse
    {   
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select("o.id, o.name, SUM(r.peoples) AS peoples")
            ->from(Organization::class, "o")
            ->join("o.buildings", "b")
            ->join("b.rooms", "r")
            ->groupBy("o.id")
            ->where("o.id = :id")
            ->setParameter("id", $id->toBinary());
            
        return new JsonResponse($queryBuilder->getQuery()->getSingleResult());
    }
    
    #[Route(
        path: "/{id}/buildings",
        name: "organization_show_buildings",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getBuildingsOfOrganizationById(EntityManagerInterface $entityManager, Ulid $id, Request $request): JsonResponse
    {
        $query = $request->query;
        
        $queryBuilder = $entityManager->createQueryBuilder();
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
        
        return new JsonResponse([
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ]);
    }
    
    #[Route(
        path: "",
        name: "organization_save",
        methods: ["POST"]
    )]
    public function saveOrganization(Request $request): JsonResponse
    {
        return parent::save($request);
    }

    #[Route(
        path: "/{id}",
        name: "organization_update",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["PUT"]
    )]
    public function updateOrganization(Organization $organization, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $body = json_decode($request->getContent() ?? "");

        if (!$body->name)
            throw new BadRequestHttpException("Missing arguments");

        $organization
            ->setName($body->name);

        $entityManager->flush();

        return new JsonResponse($organization);
    }

    #[Route(
        path: "/{id}",
        name: "organization_delete",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["DELETE"]
    )]
    public function deleteOrganization(Organization $organization): JsonResponse
    {
        return parent::delete($organization);
    }
}
