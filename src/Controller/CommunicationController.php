<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Console de communication DSR / Saferoads -> enseignants (§17 à §20).
 * MVP : publication / notification institutionnelle, pas de messagerie instantanée.
 */
#[Route('/communication')]
final class CommunicationController extends PortalController
{
    #[Route('', name: 'com_index')]
    public function index(): Response
    {
        $communications = $this->data->communications();
        $published = array_filter($communications, fn ($c) => $c['status'] === 'publiee');

        return $this->render('communication/index.html.twig', [
            'communications' => $communications,
            'recipients'     => array_sum(array_column($published, 'recipients')),
            'reads'          => array_sum(array_column($published, 'reads')),
        ]);
    }

    #[Route('/nouvelle', name: 'com_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $values = ['title' => '', 'body' => '', 'audience' => 'all', 'target' => '', 'link' => '', 'when' => 'now', 'publishAt' => '', 'email' => '1'];
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('com_new', $request->request->getString('_token'))) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }
            // Case e-mail décochée = absente du POST
            $values = array_merge($values, ['email' => ''], array_map('trim', $request->request->all('com')));

            if ($values['title'] === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            }
            if ($values['body'] === '') {
                $errors['body'] = 'Le contenu est obligatoire.';
            }
            if ($values['audience'] !== 'all' && $values['target'] === '') {
                $errors['target'] = 'Choisissez le segment ciblé.';
            }
            if ($values['when'] === 'later' && $values['publishAt'] === '') {
                $errors['publishAt'] = 'Indiquez la date de publication.';
            }

            if (!$errors) {
                $this->addFlash('success', sprintf(
                    '« %s » %s. (Démo : la communication n\'est pas enregistrée tant que la base n\'est pas branchée.)',
                    $values['title'],
                    $values['when'] === 'later' ? 'est planifiée' : 'est publiée'
                ));

                return $this->redirectToRoute('com_index');
            }
        }

        return $this->render('communication/new.html.twig', [
            'values'    => $values,
            'errors'    => $errors,
            'audiences' => $this->data->audiences(),
            'regions'   => $this->data->regions(),
            'departments' => array_reduce($this->data->regions(), fn (array $carry, array $r) => $carry + array_combine(array_keys($r['departments']), array_map(fn ($c, $n) => "$c — $n", array_keys($r['departments']), $r['departments'])), []),
            'levels'    => $this->data->levels(),
            'campaigns' => $this->data->campaigns(),
            'establishments' => $this->data->establishments(),
            'resources' => $this->data->resources(),
            'modules'   => $this->data->moocModules(),
        ]);
    }
}
