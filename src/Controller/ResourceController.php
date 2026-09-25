<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Centre de ressources pédagogiques (§9) avec filtres enseignants (§10).
 */
#[Route('/ressources')]
final class ResourceController extends PortalController
{
    #[Route('', name: 'resources_index')]
    public function index(Request $request): Response
    {
        $filters = [
            'level'    => $request->query->getString('niveau'),
            'theme'    => $request->query->getString('thematique'),
            'type'     => $request->query->getString('type'),
            'campaign' => $request->query->getString('campagne'),
        ];

        // Ressources regroupées par usage : préparer, introduire, débriefer, approfondir
        $resources = $this->data->resourcesWhere($filters);
        $groups = [];
        foreach ($this->data->resourceUsages() as $key => $usage) {
            $groups[$key] = $usage + ['items' => array_filter($resources, fn (array $r) => $r['usage'] === $key)];
        }

        return $this->render('resources/index.html.twig', [
            'usageGroups' => $groups,
            'filters'   => $filters,
            'total'     => count($resources),
            'levels'    => $this->data->levels(),
            'themes'    => $this->data->themes(),
            'types'     => $this->data->resourceTypes(),
            'campaigns' => $this->data->campaigns(),
        ]);
    }

    #[Route('/{slug}', name: 'resources_show')]
    public function show(string $slug): Response
    {
        $resource = $this->data->resources()[$slug] ?? throw $this->createNotFoundException();

        // Ressources liées : même thématique ou même usage
        $related = array_filter(
            $this->data->resources(),
            fn (array $r) => $r['slug'] !== $slug && (($resource['theme'] && $r['theme'] === $resource['theme']) || $r['usage'] === $resource['usage'])
        );

        return $this->render('resources/show.html.twig', [
            'resource'  => $resource,
            'related'   => array_slice($related, 0, 3),
            'usages'    => $this->data->resourceUsages(),
            'types'     => $this->data->resourceTypes(),
            'themes'    => $this->data->themes(),
            'campaigns' => $this->data->campaigns(),
        ]);
    }
}
