<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Connexion des comptes professionnels et redirection vers l'espace du rôle.
 */
final class SecurityController extends PortalController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $auth): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_space');
        }

        return $this->render('security/login.html.twig', [
            'last_email' => $auth->getLastUsername(),
            'error'      => $auth->getLastAuthenticationError(),
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): never
    {
        // Intercepté par le pare-feu (security.yaml)
        throw new \LogicException('Géré par le pare-feu.');
    }

    /** Après connexion : chaque profil arrive sur son propre espace. */
    #[Route('/espace', name: 'app_space')]
    public function space(): RedirectResponse
    {
        return match (true) {
            $this->isGranted('ROLE_ENSEIGNANT')         => $this->redirectToRoute('teacher_dashboard'),
            $this->isGranted('ROLE_CHEF_ETABLISSEMENT') => $this->redirectToRoute('school_dashboard'),
            $this->isGranted('ROLE_BACKOFFICE')         => $this->redirectToRoute('admin_dashboard'),
            $this->isGranted('ROLE_PILOTAGE')           => $this->redirectToRoute('pilot_dashboard'),
            default                                     => $this->redirectToRoute('app_home'),
        };
    }
}
