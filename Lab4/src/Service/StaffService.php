<?php

namespace App\Service;

use App\Entity\Staff;
use App\Exception\BadRequestException;
use App\Repository\HallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class StaffService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService,
        private HallRepository $hallRepository
    ) {}

    public function createStaff(string $name, string $position, int $hallId): Staff
    {
        $hall = $this->hallRepository->find($hallId);
        if (!$hall) {
            throw new BadRequestException("Hall with id $hallId not found", Response::HTTP_NOT_FOUND);
        }

        $staff = new Staff();
        $staff->setName($name);
        $staff->setPosition($position);
        $staff->setHall($hall);

        $this->requestCheckerService->validateRequestDataByConstraints($staff);

        $this->entityManager->persist($staff);
        return $staff;
    }

    public function updateStaff(Staff $staff, array $data): void
    {
        if (isset($data['name'])) $staff->setName($data['name']);
        if (isset($data['position'])) $staff->setPosition($data['position']);

        if (isset($data['hall_id'])) {
            $hall = $this->hallRepository->find($data['hall_id']);
            if (!$hall) throw new BadRequestException("Hall with id {$data['hall_id']} not found", Response::HTTP_NOT_FOUND);
            $staff->setHall($hall);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($staff);
    }
}