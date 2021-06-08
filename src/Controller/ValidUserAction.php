<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidUserAction
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, UserRepository $userRepository)
    {


        $tokenValidation = $request->get('tokenValidation');
        $user = $userRepository->findOneBy(['tokenValidation' => $tokenValidation]);

        if ($user === null) {
            return new JsonResponse(['error' => 'Your token is not valid'], Response::HTTP_BAD_REQUEST);
        }
        if ($user->getIsEnabled() === true) {
            return new JsonResponse(["messasge" => 'Your account is already validated'], Response::HTTP_OK);
        }
        if ($user->getTokenValidationExpireAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'Your token is expired, please try again'], Response::HTTP_BAD_REQUEST);
        }

        $user->setIsEnabled(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(["success" => "Your account is validated !"], Response::HTTP_OK);
    }
}