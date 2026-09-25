<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Centre de messages enseignant (§17) : communications DSR / Saferoads, statut lu / non lu.
 */
#[Route('/messages')]
final class MessageController extends PortalController
{
    #[Route('', name: 'messages_index')]
    public function index(): Response
    {
        return $this->render('messages/index.html.twig', [
            'messages' => $this->data->inbox(),
        ]);
    }

    #[Route('/{id}', name: 'messages_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $message = $this->data->inbox()[$id] ?? throw $this->createNotFoundException();

        // Ouvrir le message le marque comme consulté (READ_STATUS)
        $this->data->markRead($id);

        return $this->render('messages/show.html.twig', ['message' => $message]);
    }
}
