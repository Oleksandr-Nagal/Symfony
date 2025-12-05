<?php

declare(strict_types=1);

namespace App\ApiPlatform\Extension;

use App\Entity\Client;

class ClientExtension extends UserRelationExtension
{
    public function getResourceClass(): string
    {
        return Client::class;
    }
}
