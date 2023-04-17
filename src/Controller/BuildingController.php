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
    public function getAllBuilding(Request $request): JsonResponse
    {
        return parent::getAll($request);
    }

    #[Route(
        path: "/{id}",
        name: "building_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getBuildingById(Ulid $id): JsonResponse
    {
        return parent::getById($id);
    }
    
    #[Route(
        path: "/{id}/rooms",
        name: "building_show_rooms",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getRoomsOfBuildingById(Request $request, Ulid $id): JsonResponse
    {
        return new JsonResponse($this->repository->getRoomsOfBuildingById($request, $id));
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
