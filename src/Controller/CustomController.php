<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CustomController extends AbstractController
{

    protected string $entityName;
    protected EntityManagerInterface $entityManager;
    protected $repository;
    protected Request $request;
    protected SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityName = preg_replace(
            ["/^App\\\Controller/", "/Controller$/"],
            ["App\\Entity"],
            get_class($this)
        );

        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($this->entityName);
        $this->serializer = $serializer;
    }

    public function getAll(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $query = $request->query;
        $search = $query->get("search", "");
        
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select("e")
            ->from($this->entityName, "e")
            ->where("e.name LIKE :search")
            ->setParameter("search", "%$search%")
            ->setFirstResult($query->get("offset", "0"))
            ->setMaxResults($query->get("limit", "50"));
        
        $paginator = new Paginator($queryBuilder);
        
        return new JsonResponse([
            "count" => count($paginator),
            "datas" => $paginator->getQuery()->getResult()
        ]);
    }

    public function getById($entity): JsonResponse
    {
        return new JsonResponse($entity);
    }

    public function save(Request $request, array $context = []): JsonResponse
    {
        $entity = $this->serializer->deserialize($request->getContent(), $this->entityName, "json", $context);
        
        $this->repository->save($entity, true);

        return new JsonResponse($entity, 201);
    }

    public function delete($entity): JsonResponse
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse($this->repository->findAll());
    }
}
