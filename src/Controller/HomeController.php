<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Page d'accueil publique du portail professionnel Saferoads.
 *
 * Conformément à la note de cadrage (§14, §29), elle n'affiche ni données de joueurs
 * ni classement : uniquement des chiffres nationaux agrégés.
 */
final class HomeController extends PortalController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $sessions = $this->data->sessions();

        return $this->render('home/index.html.twig', [
            'stats'     => $this->data->aggregate($sessions),
            'nextCode'  => 'SR-5832',
        ]);
    }
}
