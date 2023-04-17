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
    public function getAllOrganization(Request $request): JsonResponse
    {
        return parent::getAll($request);
    }

    #[Route(
        path: "/{id}",
        name: "organization_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getOrganizationById(Ulid $id): JsonResponse
    {   
        return parent::getById($id);
    }
    
    #[Route(
        path: "/{id}/buildings",
        name: "organization_show_buildings",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getBuildingsOfOrganizationById(Request $request, Ulid $id): JsonResponse
    {
        return new JsonResponse($this->repository->getBuildingsOfOrganizationById($request, $id));
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
