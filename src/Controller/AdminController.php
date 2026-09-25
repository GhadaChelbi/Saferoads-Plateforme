<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Back-office Saferoads (§22) : gestion des référentiels, mini-CMS des ressources (§10),
 * administration du MOOC (§12), rôles et permissions (§21), contrôle qualité des données (§26-27).
 */
#[Route('/admin')]
final class AdminController extends PortalController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $sections = [];
        foreach ($this->sections() as $key => $section) {
            $sections[$key] = $section + ['count' => count($section['rows'])];
        }

        return $this->render('admin/dashboard.html.twig', [
            'sections' => $sections,
            'stats'    => $this->data->aggregate($this->data->sessions()),
            'mooc'     => $this->data->moocStats(),
        ]);
    }

    #[Route('/roles', name: 'admin_roles')]
    public function roles(): Response
    {
        return $this->render('admin/roles.html.twig', [
            'permissions' => $this->data->permissions(),
            'roles'       => ['Enseignant', "Chef d'établissement", 'DSR', 'Admin Saferoads'],
        ]);
    }

    #[Route('/qualite', name: 'admin_quality')]
    public function quality(): Response
    {
        return $this->render('admin/quality.html.twig');
    }

    #[Route('/mooc', name: 'admin_mooc')]
    public function mooc(): Response
    {
        return $this->render('admin/mooc.html.twig', [
            'modules' => $this->data->moocModules(),
            'stats'   => $this->data->moocStats(),
        ]);
    }

    /** Mini-CMS : ajouter une ressource pédagogique (§10). */
    #[Route('/ressources/nouvelle', name: 'admin_resource_new', methods: ['GET', 'POST'])]
    public function newResource(Request $request): Response
    {
        $values = ['title' => '', 'summary' => '', 'usage' => 'preparer', 'type' => 'fiche', 'levels' => [], 'theme' => '', 'campaign' => '', 'video' => '', 'publishAt' => date('Y-m-d'), 'status' => 'brouillon'];
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('resource_new', $request->request->getString('_token'))) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }
            $posted = $request->request->all('res');
            $values = array_merge($values, $posted, ['levels' => $posted['levels'] ?? []]);

            if (trim($values['title']) === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            }
            if ($values['video'] !== '' && !filter_var($values['video'], FILTER_VALIDATE_URL)) {
                $errors['video'] = "L'adresse de la vidéo n'est pas valide.";
            }

            if (!$errors) {
                $this->addFlash('success', sprintf('Ressource « %s » enregistrée en %s. (Démo : non persistée.)', $values['title'], $values['status'] === 'publiee' ? 'publication' : 'brouillon'));

                return $this->redirectToRoute('admin_list', ['section' => 'ressources']);
            }
        }

        return $this->render('admin/resource_form.html.twig', [
            'values'    => $values,
            'errors'    => $errors,
            'usages'    => $this->data->resourceUsages(),
            'types'     => $this->data->resourceTypes(),
            'levels'    => $this->data->levels(),
            'themes'    => $this->data->themes(),
            'campaigns' => $this->data->campaigns(),
        ]);
    }

    /** Liste générique d'une section de gestion. */
    #[Route('/{section}', name: 'admin_list', requirements: ['section' => 'enseignants|etablissements|sessions|campagnes|ressources|communications'])]
    public function list(string $section): Response
    {
        return $this->render('admin/list.html.twig', ['key' => $section] + $this->sections()[$section]);
    }

    // ------------------------------------------------------------------

    /** Définition des sections : titre, colonnes, lignes. */
    private function sections(): array
    {
        $d = $this->data;
        $establishments = $d->establishments();
        $regions = $d->regions();
        $teachers = $d->teachers();
        $campaigns = $d->campaigns();
        $usages = $d->resourceUsages();
        $types = $d->resourceTypes();

        return [
            'enseignants' => [
                'title' => 'Enseignants', 'icon' => '👩‍🏫', 'new' => 'Inviter un enseignant',
                'columns' => ['Nom', 'E-mail professionnel', 'Établissement', 'Discipline', 'MOOC'],
                'rows' => array_map(fn ($t) => [$t['name'], $t['email'], $establishments[$t['establishment']]['name'], $t['subject'], $t['mooc'] . ' %'], $teachers),
            ],
            'etablissements' => [
                'title' => 'Établissements', 'icon' => '🏫', 'new' => 'Ajouter un établissement',
                'columns' => ['Nom', 'Type', 'Commune', 'Département', 'Région'],
                'rows' => array_map(fn ($e) => [$e['name'], $e['type'], $e['city'], $e['dept'], $regions[$e['region']]['name']], $establishments),
            ],
            'sessions' => [
                'title' => 'Sessions', 'icon' => '🎮', 'new' => null,
                'note' => 'Les codes session sont générés par AC avec son dev (§7) : la plateforme les reçoit et les rattache.',
                'columns' => ['Code', 'Date', 'Enseignant', 'Établissement', 'Niveau', 'Statut'],
                'rows' => array_map(fn ($s) => [$s['code'], $s['date']->format('d/m/Y H:i'), $teachers[$s['teacher']]['name'], $establishments[$s['establishment']]['name'], $s['level'], $s['statusLabel']], $d->sessions()),
            ],
            'campagnes' => [
                'title' => 'Campagnes', 'icon' => '📣', 'new' => 'Créer une campagne',
                'columns' => ['Nom', 'Territoire', 'Début', 'Fin', 'Statut'],
                'rows' => array_map(fn ($c) => [$c['name'], $c['territory'], (new \DateTime($c['start']))->format('d/m/Y'), (new \DateTime($c['end']))->format('d/m/Y'), $c['status']], $campaigns),
            ],
            'ressources' => [
                'title' => 'Ressources pédagogiques', 'icon' => '📚', 'new' => 'Nouvelle ressource', 'newRoute' => 'admin_resource_new',
                'columns' => ['Titre', 'Usage', 'Type', 'Niveaux', 'Publication', 'Statut'],
                'rows' => array_map(fn ($r) => [$r['title'], $usages[$r['usage']]['label'], $types[$r['type']], $r['levels'] ? implode(', ', $r['levels']) : 'Tous', $r['publishedAt']->format('d/m/Y'), 'Publiée'], $d->resources()),
            ],
            'communications' => [
                'title' => 'Communications', 'icon' => '✉️', 'new' => 'Nouvelle communication', 'newRoute' => 'com_new',
                'columns' => ['Titre', 'Émetteur', 'Destinataires', 'Date', 'Consultation', 'Statut'],
                'rows' => array_map(fn ($c) => [$c['title'], $c['source'], $c['audience'], (new \DateTime($c['date']))->format('d/m/Y'), $c['reads'] . ' / ' . $c['recipients'], $c['status'] === 'publiee' ? 'Publiée' : 'Planifiée'], $d->communications()),
            ],
        ];
    }
}
