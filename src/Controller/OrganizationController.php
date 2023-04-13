<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OrganizationController extends AbstractController
{
    
    #[Route(
        path: "/organization",
        name: "organization_show_all",
        methods: ["GET"])]
      public function getOrganization(OrganizationRepository $repository): JsonResponse {
        $organizations = $repository->findAll();
        return new JsonResponse($organizations);
      }
      
      #[Route(
        path: "/organization/{id}",
        name: "organization_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"])]
      public function getOrganizationById(Organization $organization): JsonResponse {
        return new JsonResponse($organization);
      }
    
}
