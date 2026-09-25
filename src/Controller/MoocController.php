<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * MOOC Saferoads pour les enseignants (§11) : catalogue, leçons, quiz, progression.
 */
#[Route('/formation')]
final class MoocController extends PortalController
{
    #[Route('', name: 'mooc_index')]
    public function index(): Response
    {
        $modules = $this->data->moocModules();
        $done    = count(array_filter($modules, fn (array $m) => $m['state'] === 'done'));
        $teacher = $this->profile()['teacher'];

        return $this->render('mooc/index.html.twig', [
            'modules'  => $modules,
            'done'     => $done,
            'progress' => $teacher ? $this->data->teachers()[$teacher]['mooc'] : (int) round($done / count($modules) * 100),
            'resume'   => current(array_filter($modules, fn (array $m) => $m['state'] !== 'done')),
        ]);
    }

    #[Route('/{slug}', name: 'mooc_module')]
    public function module(string $slug): Response
    {
        $modules = $this->data->moocModules();
        $module  = $modules[$slug] ?? throw $this->createNotFoundException();
        $slugs   = array_keys($modules);
        $index   = array_search($slug, $slugs, true);

        return $this->render('mooc/module.html.twig', [
            'module'   => $module,
            'previous' => $index > 0 ? $modules[$slugs[$index - 1]] : null,
            'next'     => $modules[$slugs[$index + 1] ?? ''] ?? null,
        ]);
    }
}
