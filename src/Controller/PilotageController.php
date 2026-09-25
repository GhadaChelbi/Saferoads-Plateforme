<?php

namespace App\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Cockpit de pilotage Saferoads / DSR (§13 à §16).
 * Lecture territoriale France > Région > Département > Établissement > Session.
 * Aucun classement public : les listes sont triées par ordre alphabétique.
 */
#[Route('/pilotage')]
final class PilotageController extends PortalController
{
    #[Route('', name: 'pilot_dashboard')]
    public function dashboard(Request $request): Response
    {
        $filters  = $this->filters($request);
        $sessions = $this->data->sessionsWhere($filters);
        $stats    = $this->data->aggregate($sessions);

        return $this->render('pilotage/dashboard.html.twig', [
            'filters'    => $filters,
            'stats'      => $stats,
            'themeFocus' => $filters['theme'] ? current(array_filter($stats['themes'], fn ($t) => $t['key'] === $filters['theme'])) : null,
            'drill'      => $this->drillDown($filters, $sessions),
            'breadcrumb' => $this->breadcrumb($filters),
            'monthly'    => $this->data->monthlyPlayers(),
            'errors'     => $this->data->frequentErrors(),
        ] + $this->referentials());
    }

    #[Route('/etablissements', name: 'pilot_establishments')]
    public function establishments(Request $request): Response
    {
        $filters = $this->filters($request);
        $rows = [];
        foreach ($this->data->establishments() as $e) {
            if (($filters['region'] && $e['region'] !== $filters['region']) || ($filters['dept'] && $e['dept'] !== $filters['dept'])) {
                continue;
            }
            $sessions = $this->data->sessionsWhere(['establishment' => $e['id']] + $filters);
            $levels = array_unique(array_column($sessions, 'level'));
            sort($levels);
            $rows[] = $e + ['stats' => $this->data->aggregate($sessions), 'levels' => $levels];
        }
        usort($rows, fn (array $a, array $b) => strcoll($a['name'], $b['name']));

        return $this->render('pilotage/establishments.html.twig', [
            'rows'    => $rows,
            'filters' => $filters,
        ] + $this->referentials());
    }

    #[Route('/campagnes', name: 'pilot_campaigns')]
    public function campaigns(): Response
    {
        $rows = [];
        foreach ($this->data->campaigns() as $c) {
            $rows[] = $c + ['stats' => $this->data->aggregate($this->data->sessionsWhere(['campaign' => $c['slug']]))];
        }

        return $this->render('pilotage/campaigns.html.twig', ['rows' => $rows]);
    }

    #[Route('/campagnes/{slug}', name: 'pilot_campaign')]
    public function campaign(string $slug): Response
    {
        $campaign = $this->data->campaigns()[$slug] ?? throw $this->createNotFoundException();
        $sessions = $this->data->sessionsWhere(['campaign' => $slug]);

        // Répartition par région
        $regions = [];
        foreach ($this->data->regions() as $key => $region) {
            $list = $this->data->sessionsWhere(['campaign' => $slug, 'region' => $key]);
            if ($list) {
                $regions[] = ['key' => $key, 'name' => $region['name'], 'stats' => $this->data->aggregate($list)];
            }
        }

        return $this->render('pilotage/campaign.html.twig', [
            'campaign'       => $campaign,
            'stats'          => $this->data->aggregate($sessions),
            'regions'        => $regions,
            'establishments' => $this->data->establishments(),
        ]);
    }

    #[Route('/reporting', name: 'pilot_reporting')]
    public function reporting(Request $request): Response
    {
        $filters  = $this->filters($request);
        $sessions = $this->data->sessionsWhere($filters);

        return $this->render('pilotage/reporting.html.twig', [
            'filters'  => $filters,
            'sessions' => $sessions,
            'stats'    => $this->data->aggregate($sessions),
            'establishmentsList' => $this->data->establishments(),
        ] + $this->referentials());
    }

    /** Export des données traitées, en tenant compte des filtres appliqués (§16). */
    #[Route('/reporting/export.{format}', name: 'pilot_export', requirements: ['format' => 'csv|xlsx'])]
    public function export(Request $request, string $format): Response
    {
        $rows = $this->exportRows($this->data->sessionsWhere($this->filters($request)));
        $filename = 'saferoads-reporting-' . date('Y-m-d') . '.' . $format;

        if ($format === 'csv') {
            $response = new StreamedResponse(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF"); // BOM : accents corrects à l'ouverture dans Excel
                foreach ($rows as $row) {
                    fputcsv($out, $row, ';', '"', '');
                }
                fclose($out);
            });
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        } else {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->setTitle('Reporting');
            $sheet->fromArray($rows);
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $response = new StreamedResponse(fn () => (new Xlsx($spreadsheet))->save('php://output'));
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    // ------------------------------------------------------------------

    /** Filtres communs : période, campagne, région, département, établissement, niveau, thématique. */
    private function filters(Request $request): array
    {
        $q = $request->query;
        $filters = [
            'from'          => $q->getString('du'),
            'to'            => $q->getString('au'),
            'campaign'      => $q->getString('campagne'),
            'region'        => $q->getString('region'),
            'dept'          => $q->getString('departement'),
            'establishment' => $q->getString('etablissement'),
            'level'         => $q->getString('niveau'),
            'theme'         => $q->getString('thematique'),
        ];

        // Cohérence de la hiérarchie : un département implique sa région
        if ($filters['dept'] && !$filters['region']) {
            foreach ($this->data->regions() as $key => $r) {
                if (isset($r['departments'][$filters['dept']])) {
                    $filters['region'] = $key;
                }
            }
        }

        return $filters;
    }

    private function referentials(): array
    {
        return [
            'regions'        => $this->data->regions(),
            'campaigns'      => $this->data->campaigns(),
            'levels'         => $this->data->levels(),
            'themes'         => $this->data->themes(),
            'establishments' => $this->data->establishments(),
        ];
    }

    /** Niveau suivant de la lecture territoriale, selon les filtres. */
    private function drillDown(array $filters, array $sessions): array
    {
        $regions = $this->data->regions();
        $rows = [];

        if ($filters['establishment']) {
            $level = 'Sessions';
            foreach ($sessions as $s) {
                $rows[] = ['name' => $s['code'] . ' — ' . $s['class'], 'stats' => $this->data->aggregate([$s]), 'params' => null];
            }
        } elseif ($filters['dept']) {
            $level = 'Établissements';
            foreach ($this->data->establishments() as $e) {
                if ($e['dept'] === $filters['dept']) {
                    $list = array_filter($sessions, fn ($s) => $s['establishment'] === $e['id']);
                    $rows[] = ['name' => $e['name'], 'stats' => $this->data->aggregate($list), 'params' => ['etablissement' => $e['id']]];
                }
            }
        } elseif ($filters['region']) {
            $level = 'Départements';
            foreach ($regions[$filters['region']]['departments'] as $code => $name) {
                $list = $this->data->sessionsWhere(['dept' => $code] + $filters);
                $rows[] = ['name' => "$name ($code)", 'stats' => $this->data->aggregate($list), 'params' => ['departement' => $code]];
            }
        } else {
            $level = 'Régions';
            foreach ($regions as $key => $r) {
                $list = $this->data->sessionsWhere(['region' => $key] + $filters);
                $rows[] = ['name' => $r['name'], 'stats' => $this->data->aggregate($list), 'params' => ['region' => $key]];
            }
        }

        usort($rows, fn (array $a, array $b) => strcoll($a['name'], $b['name']));

        return ['level' => $level, 'rows' => $rows];
    }

    /** Fil d'Ariane territorial : France > Région > Département > Établissement. */
    private function breadcrumb(array $filters): array
    {
        $crumbs = [['label' => 'France', 'params' => []]];
        $regions = $this->data->regions();

        if ($filters['region']) {
            $crumbs[] = ['label' => $regions[$filters['region']]['name'], 'params' => ['region' => $filters['region']]];
        }
        if ($filters['dept']) {
            $crumbs[] = ['label' => $regions[$filters['region']]['departments'][$filters['dept']] ?? $filters['dept'], 'params' => ['region' => $filters['region'], 'departement' => $filters['dept']]];
        }
        if ($filters['establishment']) {
            $e = $this->data->establishments()[$filters['establishment']] ?? null;
            $crumbs[] = ['label' => $e['name'] ?? '?', 'params' => ['region' => $filters['region'], 'departement' => $filters['dept'], 'etablissement' => $filters['establishment']]];
        }

        return $crumbs;
    }

    /** Lignes d'export : une ligne par session, agrégats uniquement (aucune donnée élève). */
    private function exportRows(array $sessions): array
    {
        $themes = $this->data->themes();
        $establishments = $this->data->establishments();
        $campaigns = $this->data->campaigns();
        $regions = $this->data->regions();

        $rows = [array_merge(
            ['Code session', 'Date', 'Statut', 'Établissement', 'Département', 'Région', 'Niveau', 'Campagne', 'Parcours', 'Participants', 'Parcours terminés', 'Complétion (%)', 'Réussite (%)', 'Temps moyen (min)'],
            array_map(fn ($t) => "Réussite $t (%)", array_values($themes))
        )];

        foreach ($sessions as $s) {
            $e = $establishments[$s['establishment']];
            $rows[] = array_merge([
                $s['code'], $s['date']->format('d/m/Y'), $s['statusLabel'], $e['name'], $e['dept'], $regions[$e['region']]['name'],
                $s['level'], $campaigns[$s['campaign']]['name'], $s['path'], $s['participants'], $s['completed'],
                $s['completion'] ?? '', $s['success'] ?? '', $s['avgMinutes'] ?? '',
            ], array_map(fn ($k) => $s['rates'][$k] ?? '', array_keys($themes)));
        }

        return $rows;
    }
}
