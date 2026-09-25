<?php

namespace App\Controller;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace enseignant (§4, §5, §8).
 * Règle de visibilité : l'enseignant ne voit QUE ses propres classes et sessions.
 */
#[Route('/enseignant')]
final class TeacherController extends PortalController
{
    /** Adresse ouverte par le QR Code dans le serious game (à confirmer avec le Dev Game). */
    private const JOIN_URL = 'https://jeu.saferoads.fr/rejoindre?code=';

    #[Route('', name: 'teacher_dashboard')]
    public function dashboard(): Response
    {
        $teacherId = $this->profile()['teacher'];
        $sessions  = $this->data->sessionsWhere(['teacher' => $teacherId]);
        $stats     = $this->data->aggregate($sessions);

        // Prochaine séance = session planifiée la plus proche
        $planned = array_filter($sessions, fn (array $s) => $s['status'] === 'planifiee');
        usort($planned, fn (array $a, array $b) => $a['date'] <=> $b['date']);

        return $this->render('teacher/dashboard.html.twig', [
            'next'     => $planned[0] ?? null,
            'stats'    => $stats,
            'recent'   => array_slice(array_filter($sessions, fn (array $s) => $s['status'] === 'terminee'), 0, 3),
            'teacher'  => $this->data->teachers()[$teacherId],
            'debrief'  => $stats['weakest'] ? $this->data->debriefFor($stats['weakest']['key']) : null,
            'messages' => array_slice($this->data->inbox(), 0, 3, true),
            'module'   => current(array_filter($this->data->moocModules(), fn (array $m) => $m['state'] !== 'done')),
        ]);
    }

    #[Route('/sessions', name: 'teacher_sessions')]
    public function sessions(): Response
    {
        return $this->render('teacher/sessions.html.twig', [
            'sessions'  => $this->data->sessionsWhere(['teacher' => $this->profile()['teacher']]),
            'campaigns' => $this->data->campaigns(),
        ]);
    }

    #[Route('/sessions/{code}', name: 'teacher_session', requirements: ['code' => 'SR-\d{4}'])]
    public function session(string $code): Response
    {
        $session = $this->data->sessions()[$code] ?? null;

        // Une session d'un autre enseignant est introuvable pour lui (pas de fuite d'information)
        if (!$session || $session['teacher'] !== $this->profile()['teacher']) {
            throw $this->createNotFoundException('Session introuvable.');
        }

        $stats = $this->data->aggregate([$session]);

        $qr = (new Builder(
            writer: new SvgWriter(),
            data: self::JOIN_URL . $code,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 240,
            margin: 0,
        ))->build();

        return $this->render('teacher/session.html.twig', [
            'session'       => $session,
            'stats'         => $stats,
            'qr'            => $qr->getDataUri(),
            'joinUrl'       => self::JOIN_URL . $code,
            'campaign'      => $this->data->campaigns()[$session['campaign']],
            'establishment' => $this->data->establishments()[$session['establishment']],
            'debrief'       => $stats['weakest'] ? $this->data->debriefFor($stats['weakest']['key']) : null,
            'preparation'   => $this->data->resourcesWhere(['usage' => 'preparer']),
        ]);
    }

    #[Route('/resultats', name: 'teacher_results')]
    public function results(): Response
    {
        $sessions = array_filter(
            $this->data->sessionsWhere(['teacher' => $this->profile()['teacher']]),
            fn (array $s) => $s['status'] === 'terminee'
        );

        // Regroupement par classe
        $classes = [];
        foreach ($sessions as $s) {
            $classes[$s['class']][] = $s;
        }
        ksort($classes);

        $byClass = [];
        foreach ($classes as $class => $list) {
            $byClass[$class] = ['sessions' => $list, 'stats' => $this->data->aggregate($list)];
        }

        $stats = $this->data->aggregate($sessions);

        return $this->render('teacher/results.html.twig', [
            'stats'   => $stats,
            'byClass' => $byClass,
            'debrief' => $stats['weakest'] ? $this->data->debriefFor($stats['weakest']['key']) : null,
            'themes'  => $this->data->themes(),
        ]);
    }
}
