<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace chef d'établissement (§6).
 * Périmètre strict : uniquement les enseignants, classes et sessions de SON établissement.
 */
#[Route('/etablissement')]
final class SchoolController extends PortalController
{
    #[Route('', name: 'school_dashboard')]
    public function dashboard(): Response
    {
        $establishmentId = $this->profile()['establishment'];
        $sessions = $this->data->sessionsWhere(['establishment' => $establishmentId]);

        // Enseignants de l'établissement, avec leur utilisation du dispositif
        $teachers = [];
        foreach ($this->data->teachers() as $t) {
            if ($t['establishment'] !== $establishmentId) {
                continue;
            }
            $own = array_filter($sessions, fn (array $s) => $s['teacher'] === $t['id']);
            // Dernière séance réalisée (les séances planifiées ne comptent pas)
            $done = array_filter($own, fn (array $s) => $s['status'] === 'terminee');
            $last = $done ? max(array_column($done, 'date')) : null;
            $teachers[] = $t + [
                'sessions' => count($own),
                'done'     => count($done),
                'last'     => $last,
                'active'   => (bool) $own,
            ];
        }

        // Utilisation par niveau
        $levels = [];
        foreach ($this->data->levels() as $level) {
            $levels[$level] = count(array_filter($sessions, fn (array $s) => $s['level'] === $level));
        }

        return $this->render('school/dashboard.html.twig', [
            'establishment' => $this->data->establishments()[$establishmentId],
            'stats'         => $this->data->aggregate($sessions),
            'teachers'      => $teachers,
            'levels'        => array_filter($levels),
            'upcoming'      => array_filter($sessions, fn (array $s) => $s['status'] !== 'terminee'),
            'news'          => array_slice($this->data->inbox(), 0, 2, true),
        ]);
    }

    #[Route('/sessions', name: 'school_sessions')]
    public function sessions(): Response
    {
        $establishmentId = $this->profile()['establishment'];

        return $this->render('school/sessions.html.twig', [
            'establishment' => $this->data->establishments()[$establishmentId],
            'sessions'      => $this->data->sessionsWhere(['establishment' => $establishmentId]),
            'teachers'      => $this->data->teachers(),
            'campaigns'     => $this->data->campaigns(),
        ]);
    }
}
