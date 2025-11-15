<?php

namespace App\Service;

class ValidatorService
{
    public function validateMovie(array $data): array
    {
        $errors = [];
        if (empty($data['title'])) $errors[] = 'Title is required';
        if (empty($data['description'])) $errors[] = 'Description is required';
        if (!isset($data['duration']) || !is_int($data['duration'])) $errors[] = 'Duration must be an integer';
        if (empty($data['releaseDate'])) $errors[] = 'ReleaseDate is required';
        return $errors;
    }

    public function validateGenre(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Name is required';
        return $errors;
    }

    public function validateActor(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Name is required';
        return $errors;
    }

    public function validateDirector(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Name is required';
        return $errors;
    }

    public function validateHall(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Name is required';
        if (!isset($data['capacity']) || !is_int($data['capacity'])) $errors[] = 'Capacity must be an integer';
        return $errors;
    }

    public function validateSession(array $data): array
    {
        $errors = [];
        if (empty($data['startTime'])) $errors[] = 'StartTime is required';
        if (!isset($data['price']) || !is_numeric($data['price'])) $errors[] = 'Price must be numeric';
        if (empty($data['movie_id'])) $errors[] = 'Movie is required';
        if (empty($data['hall_id'])) $errors[] = 'Hall is required';
        return $errors;
    }

    public function validateClient(array $data): array
    {
        $errors = [];
        if (empty($data['email'])) $errors[] = 'Email is required';
        if (empty($data['name'])) $errors[] = 'Name is required';
        return $errors;
    }

    public function validateBooking(array $data): array
    {
        $errors = [];
        if (empty($data['createdAt'])) $errors[] = 'CreatedAt is required';
        if (empty($data['client_id'])) $errors[] = 'Client is required';
        return $errors;
    }

    public function validateTicket(array $data): array
    {
        $errors = [];
        if (empty($data['seatNumber'])) $errors[] = 'SeatNumber is required';
        if (empty($data['session_id'])) $errors[] = 'Session is required';
        if (empty($data['booking_id'])) $errors[] = 'Booking is required';
        return $errors;
    }

    public function validateStaff(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Name is required';
        if (empty($data['position'])) $errors[] = 'Position is required';
        if (empty($data['hall_id'])) $errors[] = 'Hall is required';
        return $errors;
    }
}
