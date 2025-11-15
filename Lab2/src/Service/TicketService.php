<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\Session;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

class TicketService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createTicket(string $seatNumber, Session $session, Booking $booking): Ticket
    {
        $ticket = new Ticket();
        $ticket->setSeatNumber($seatNumber);
        $ticket->setSession($session);
        $ticket->setBooking($booking);
        $this->em->persist($ticket);
        $this->em->flush();
        return $ticket;
    }
}
