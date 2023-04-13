<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CustomController extends AbstractController {
  
  protected string $entityName;
  protected EntityManagerInterface $entityManager;
  protected mixed $repository;
  protected Request $request;
  protected SerializerInterface $serializer;
  
  public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer) {
    $this->entityName = preg_replace(
      ['/^App\\\Controller/', "/Controller$/"],
      ["App\\Entity"],
      get_class($this)
    );
    
    $this->entityManager = $entityManager;
    $this->repository = $entityManager->getRepository($this->entityName);
    $this->serializer = $serializer;
  }
  
  public function getAll(): JsonResponse {
    return new JsonResponse($this->repository->findAll());
  }
  
  public function getById($entity): JsonResponse {
    return new JsonResponse($entity);
  }
  
  public function save(Request $request): JsonResponse {
    $entity = $this->serializer->deserialize($request->getContent(), $this->entityName, "json");
    
    $this->repository->save($entity, true);
    
    return new JsonResponse($entity, 201);
  }
  
  public function delete($entity): JsonResponse {
    $this->repository->remove($entity);
    $this->repository->flush();
    
    return new JsonResponse(status: 204);
  }
  
}