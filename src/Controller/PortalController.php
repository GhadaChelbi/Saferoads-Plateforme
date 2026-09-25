<?php

namespace App\Controller;

use App\Data\DemoData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Base des contrôleurs de la plateforme : accès aux données et au profil connecté.
 */
abstract class PortalController extends AbstractController
{
    public function __construct(protected readonly DemoData $data)
    {
    }

    /** Profil du compte connecté (nom, rôle, enseignant / établissement rattaché). */
    protected function profile(): array
    {
        return $this->data->profile($this->getUser()?->getUserIdentifier() ?? '');
    }
}
