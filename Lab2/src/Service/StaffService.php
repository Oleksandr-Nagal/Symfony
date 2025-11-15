<?php

namespace App\Service;

use App\Entity\Staff;
use App\Entity\Hall;
use Doctrine\ORM\EntityManagerInterface;

class StaffService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createStaff(string $name, string $position, Hall $hall): Staff
    {
        $staff = new Staff();
        $staff->setName($name);
        $staff->setPosition($position);
        $staff->setHall($hall);
        $this->em->persist($staff);
        $this->em->flush();
        return $staff;
    }
}
