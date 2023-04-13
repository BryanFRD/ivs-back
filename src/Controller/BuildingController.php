<?php 

namespace App\Controller;

use App\Entity\Building;
use App\Repository\BuildingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Ulid;

#[Route(path: "/building",)]
class BuildingController extends CustomController {
  
  #[Route(
    path: "",
    name: "building_show_all",
    methods: ["GET"])]
  public function getAllBuilding(): JsonResponse {
    return parent::getAll();
  }
  
  #[Route(
    path: "/{id}",
    name: "building_show_by_id",
    requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
    methods: ["GET"])]
  public function getBuildingById(Building $building): JsonResponse {
    return parent::getById($building);
  }
  
  #[Route(
    path: "",
    name: "building_save",
    methods: ["POST"]
  )]
  public function saveBuilding(Request $request): JsonResponse {
    return parent::save($request);
  }
  
  #[Route(
    path: "/{id}",
    name: "building_update",
    requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
    methods: ["PUT"]
  )]
  public function updateBuilding(Building $building, EntityManagerInterface $entityManager, Request $request): JsonResponse {
    $body = json_decode($request->getContent() ?? "");
    
    if(!$body->name || !$body->zipcode || !$body->building)
      throw new BadRequestHttpException("Missing arguments");
    
    $building
      ->setName($body->name)
      ->setZipcode($body->zipcode)
      ->setOrganization($body->building);
    
    $entityManager->flush();
      
    return new JsonResponse($building);
  }
  
  #[Route(
    path: "/{id}",
    name: "building_delete",
    requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
    methods: ["DELETE"]
  )]
  public function deleteBuilding(Building $building): JsonResponse {
    return parent::delete($building);
  }
  
}