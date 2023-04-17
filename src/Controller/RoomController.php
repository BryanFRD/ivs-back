<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\BuildingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/room")]
class RoomController extends CustomController
{

    #[Route(
        path: "",
        name: "room_show_all",
        methods: ["GET"]
    )]
    public function getAllRoom(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        return parent::getAll($entityManager, $request);
    }

    #[Route(
        path: "/{id}",
        name: "room_show_by_id",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["GET"]
    )]
    public function getRoomById(Room $room): JsonResponse
    {
        return parent::getById($room);
    }

    #[Route(
        path: "",
        name: "room_save",
        methods: ["POST"]
    )]
    public function saveRoom(Request $request, BuildingRepository $buildingRepository): JsonResponse
    {
        $body = json_decode($request->getContent());

        if (!isset($body->name, $body->peoples))
            throw new BadRequestException("Missing arguments");

        $room = new Room();

        try {
            $room
                ->setName($body->name)
                ->setPeoples(intval($body->peoples))
                ->setBuilding($buildingRepository->find($body->building_id ?: ''));
        } catch (Exception $ignored) {
            throw new BadRequestException("Bad arguments");
        }

        $buildingRepository->save($room, true);

        return new JsonResponse($room, status: 201);
    }

    #[Route(
        path: "/{id}",
        name: "room_update",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["PUT"]
    )]
    public function updateRoom(Room $room, EntityManagerInterface $entityManager, Request $request, BuildingRepository $buildingRepository): JsonResponse
    {
        $body = json_decode($request->getContent() ?? "");

        if (!isset($body->name, $body->peoples, $body->building_id))
            throw new BadRequestHttpException("Missing arguments");

        try {
            $room
                ->setName($body->name)
                ->setPeoples($body->peoples)
                ->setBuilding($buildingRepository->find($body->building_id ?: ''));
        } catch (Exception $ignored) {
            throw new BadRequestException("Bad arguments");
        }

        $entityManager->flush();

        return new JsonResponse($room);
    }

    #[Route(
        path: "/{id}",
        name: "room_delete",
        requirements: ["id" => "[0-7][0-9A-HJKMNP-TV-Z]{25}"],
        methods: ["DELETE"]
    )]
    public function deleteRoom(Room $room): JsonResponse
    {
        return parent::delete($room);
    }
}
