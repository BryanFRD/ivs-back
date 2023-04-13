<?php

namespace App\Controller;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/room")]
class OrganizationController extends CustomController
{
    
    #[Route(
        path: "",
        name: "room_show_all",
        methods: ["GET"])]
    public function getAllRoom(): JsonResponse {
        return parent::getAll();
    }
    
    #[Route(
        path: "/{id}",
        name: "room_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"])]
    public function getRoomById(Room $room): JsonResponse {
        return parent::getById($room);
    }
    
    #[Route(
        path: "",
        name: "room_save",
        methods: ["POST"])]
    public function saveRoom(Request $request): JsonResponse {
        return parent::save($request);
    }
      
    #[Route(
        path: "/{id}",
        name: "room_update",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["PUT"]
    )]
    public function updateRoom(Room $room, EntityManagerInterface $entityManager, Request $request): JsonResponse {
        $body = json_decode($request->getContent() ?? "");
        
        if(!$body->name || !$body->peoples || !$body->building)
          throw new BadRequestHttpException("Missing arguments");
        
        $room
          ->setName($body->name)
          ->setPeoples($body->peoples)
          ->setBuilding($body->building);
        
        $entityManager->flush();
          
        return new JsonResponse($room);
    }
    
    #[Route(
        path: "/{id}",
        name: "room_delete",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["DELETE"]
      )]
      public function deleteRoom(Room $room): JsonResponse {
        return parent::delete($room);
      }
    
}
