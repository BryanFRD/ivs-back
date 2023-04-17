<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Room;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Ulid;

#[Route(path: "/building",)]
class BuildingController extends CustomController
{

    #[Route(
        path: "",
        name: "building_show_all",
        methods: ["GET"]
    )]
    public function getAllBuilding(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $query = $request->query;
        $search = $query->get("search", "");
        
        $queryBuilder = $entityManager->createQueryBuilder();
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
            
        return new JsonResponse([
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ]);
    }

    #[Route(
        path: "/{id}",
        name: "building_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getBuildingById(EntityManagerInterface $entityManager, Ulid $id): JsonResponse
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select("b.id, b.name, b.zipcode, o.id as organization_id, o.name as organization_name, SUM(r.peoples) AS peoples")
            ->from(Building::class, "b")
            ->join("b.rooms", "r")
            ->join("b.organization", "o")
            ->where("b.id = :id")
            ->groupBy("b.id")
            ->setParameter("id", $id->toBinary());
        
        return new JsonResponse($queryBuilder->getQuery()->getSingleResult());
    }
    
    #[Route(
        path: "/{id}/rooms",
        name: "building_show_rooms",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getRoomsOfBuildingById(EntityManagerInterface $entityManager, Request $request, Ulid $id): JsonResponse
    {
        $query = $request->query;
        
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('r')
            ->from(Room::class, 'r')
            ->where("r.building = :id")
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
        name: "building_save",
        methods: ["POST"]
    )]
    public function saveBuilding(Request $request, OrganizationRepository $organizationRepository): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (!isset($body->name, $body->zipcode))
            throw new BadRequestException("Missing arguments");

        $building = new Building();

        try {
            $building
                ->setName($body->name)
                ->setZipcode(intval($body->zipcode))
                ->setOrganization($organizationRepository->find($body->organization_id ?: ''));
        } catch (Exception $ignored) {
            throw new BadRequestException("Bad arguments");
        }

        $organizationRepository->save($building, true);

        return new JsonResponse($building, status: 201);
    }

    #[Route(
        path: "/{id}",
        name: "building_update",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["PUT"]
    )]
    public function updateBuilding(Building $building, EntityManagerInterface $entityManager, Request $request, OrganizationRepository $organizationRepository): JsonResponse
    {
        $body = json_decode($request->getContent() ?? "");

        if (!isset($body->name, $body->zipcode, $body->organization_id))
            throw new BadRequestHttpException("Missing arguments");

        try {
            $building
                ->setName($body->name)
                ->setZipcode(intval($body->zipcode))
                ->setOrganization($organizationRepository->find($body->organization_id ?: ''));
        } catch (Exception $ignored) {
            throw new BadRequestException("Bad arguments");
        }

        $entityManager->flush();

        return new JsonResponse($building);
    }

    #[Route(
        path: "/{id}",
        name: "building_delete",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["DELETE"]
    )]
    public function deleteBuilding(Building $building): JsonResponse
    {
        return parent::delete($building);
    }
}
