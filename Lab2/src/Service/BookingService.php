<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createBooking(\DateTime $createdAt, Client $client): Booking
    {
        $booking = new Booking();
        $booking->setCreatedAt($createdAt);
        $booking->setClient($client);
        $this->em->persist($booking);
        $this->em->flush();
        return $booking;
    }
}
