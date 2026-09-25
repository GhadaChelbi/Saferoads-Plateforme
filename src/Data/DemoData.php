<?php

namespace App\Data;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Données de démonstration statiques de la plateforme Saferoads (aucune base de données).
 *
 * Chaque méthode correspond à une future table ou requête Doctrine (§23 de la note de cadrage) :
 * ESTABLISHMENT, TEACHER, SESSION, CAMPAIGN, THEME, PEDAGOGICAL_RESOURCE, MOOC_MODULE,
 * COMMUNICATION, READ_STATUS… Les contrôleurs ne manipulent que cette classe : la remplacer
 * par des repositories suffira à brancher la vraie base.
 *
 * RGPD : aucune donnée nominative d'élève — uniquement des agrégats par session.
 */
final class DemoData
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    // ------------------------------------------------------------------
    // Comptes professionnels (PROFESSIONAL_USER)
    // ------------------------------------------------------------------

    /** Profil affiché pour un compte connecté (nom, rôle, rattachement). */
    public function profile(string $email): array
    {
        return match ($email) {
            'enseignant@saferoads.fr' => ['name' => 'Claire Martin',  'role' => 'Enseignante',           'teacher' => 'T1', 'establishment' => 'E1'],
            'chef@saferoads.fr'       => ['name' => 'Karim Benali',   'role' => "Chef d'établissement",  'teacher' => null, 'establishment' => 'E1'],
            'dsr@saferoads.fr'        => ['name' => 'Sophie Laurent', 'role' => 'DSR',                   'teacher' => null, 'establishment' => null],
            'admin@saferoads.fr'      => ['name' => 'Thomas Girard',  'role' => 'Admin Saferoads',       'teacher' => null, 'establishment' => null],
            default                   => ['name' => $email,           'role' => 'Utilisateur',           'teacher' => null, 'establishment' => null],
        };
    }

    // ------------------------------------------------------------------
    // Référentiels
    // ------------------------------------------------------------------

    /** Territoires : région -> départements. */
    public function regions(): array
    {
        return [
            'idf'  => ['name' => 'Île-de-France',              'departments' => ['75' => 'Paris', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis']],
            'ara'  => ['name' => 'Auvergne-Rhône-Alpes',       'departments' => ['69' => 'Rhône', '38' => 'Isère']],
            'hdf'  => ['name' => 'Hauts-de-France',            'departments' => ['59' => 'Nord', '62' => 'Pas-de-Calais']],
            'occ'  => ['name' => 'Occitanie',                  'departments' => ['31' => 'Haute-Garonne', '34' => 'Hérault']],
        ];
    }

    /** Niveaux scolaires concernés. */
    public function levels(): array
    {
        return ['5e', '4e', '3e', '2nde', '1re'];
    }

    /** Thématiques de sécurité routière (THEME). */
    public function themes(): array
    {
        return [
            'signalisation' => 'Signalisation',
            'priorites'     => 'Priorités',
            'vitesse'       => 'Vitesse',
            'distraction'   => 'Distraction',
            'alcool'        => 'Alcool & stupéfiants',
            'vulnerables'   => 'Usagers vulnérables',
            'equipements'   => 'Équipements (casque, ceinture)',
        ];
    }

    /** Établissements (ESTABLISHMENT). */
    public function establishments(): array
    {
        return [
            'E1'  => ['id' => 'E1',  'name' => 'Collège Jean Moulin',        'type' => 'Collège', 'city' => 'Paris 15e',     'dept' => '75', 'region' => 'idf'],
            'E2'  => ['id' => 'E2',  'name' => 'Lycée Marie Curie',          'type' => 'Lycée',   'city' => 'Nanterre',      'dept' => '92', 'region' => 'idf'],
            'E3'  => ['id' => 'E3',  'name' => 'Collège Paul Éluard',        'type' => 'Collège', 'city' => 'Saint-Denis',   'dept' => '93', 'region' => 'idf'],
            'E4'  => ['id' => 'E4',  'name' => 'Collège Les Iris',           'type' => 'Collège', 'city' => 'Villeurbanne',  'dept' => '69', 'region' => 'ara'],
            'E5'  => ['id' => 'E5',  'name' => 'Lycée Champollion',          'type' => 'Lycée',   'city' => 'Grenoble',      'dept' => '38', 'region' => 'ara'],
            'E6'  => ['id' => 'E6',  'name' => 'Collège Albert Samain',      'type' => 'Collège', 'city' => 'Roubaix',       'dept' => '59', 'region' => 'hdf'],
            'E7'  => ['id' => 'E7',  'name' => 'Lycée Robespierre',          'type' => 'Lycée',   'city' => 'Arras',         'dept' => '62', 'region' => 'hdf'],
            'E8'  => ['id' => 'E8',  'name' => 'Collège Pierre de Fermat',   'type' => 'Collège', 'city' => 'Toulouse',      'dept' => '31', 'region' => 'occ'],
            'E9'  => ['id' => 'E9',  'name' => 'Lycée Jean Mermoz',          'type' => 'Lycée',   'city' => 'Montpellier',   'dept' => '34', 'region' => 'occ'],
        ];
    }

    /** Enseignants (TEACHER) — progression MOOC en %. */
    public function teachers(): array
    {
        return [
            'T1'  => ['id' => 'T1',  'name' => 'Claire Martin',     'email' => 'c.martin@ac-paris.fr',      'establishment' => 'E1', 'subject' => 'Histoire-géo / EMC', 'mooc' => 70],
            'T2'  => ['id' => 'T2',  'name' => 'Julien Morel',      'email' => 'j.morel@ac-paris.fr',       'establishment' => 'E1', 'subject' => 'Technologie',        'mooc' => 100],
            'T3'  => ['id' => 'T3',  'name' => 'Nadia Haddad',      'email' => 'n.haddad@ac-paris.fr',      'establishment' => 'E1', 'subject' => 'EPS',                'mooc' => 35],
            'T4'  => ['id' => 'T4',  'name' => 'Luc Fontaine',      'email' => 'l.fontaine@ac-paris.fr',    'establishment' => 'E1', 'subject' => 'SVT',                'mooc' => 0],
            'T5'  => ['id' => 'T5',  'name' => 'Émilie Rousseau',   'email' => 'e.rousseau@ac-versailles.fr', 'establishment' => 'E2', 'subject' => 'SES',              'mooc' => 85],
            'T6'  => ['id' => 'T6',  'name' => 'Mehdi Saïdi',       'email' => 'm.saidi@ac-creteil.fr',     'establishment' => 'E3', 'subject' => 'Physique-chimie',    'mooc' => 50],
            'T7'  => ['id' => 'T7',  'name' => 'Anne Perrin',       'email' => 'a.perrin@ac-lyon.fr',       'establishment' => 'E4', 'subject' => 'EMC',                'mooc' => 100],
            'T8'  => ['id' => 'T8',  'name' => 'François Leroy',    'email' => 'f.leroy@ac-grenoble.fr',    'establishment' => 'E5', 'subject' => 'Mathématiques',      'mooc' => 20],
            'T9'  => ['id' => 'T9',  'name' => 'Sarah Dubois',      'email' => 's.dubois@ac-lille.fr',      'establishment' => 'E6', 'subject' => 'Histoire-géo',       'mooc' => 60],
            'T10' => ['id' => 'T10', 'name' => 'Paul Lambert',      'email' => 'p.lambert@ac-lille.fr',     'establishment' => 'E7', 'subject' => 'STI2D',              'mooc' => 90],
            'T11' => ['id' => 'T11', 'name' => 'Inès Garcia',       'email' => 'i.garcia@ac-toulouse.fr',   'establishment' => 'E8', 'subject' => 'EPS',                'mooc' => 45],
            'T12' => ['id' => 'T12', 'name' => 'Hugo Bernard',      'email' => 'h.bernard@ac-montpellier.fr', 'establishment' => 'E9', 'subject' => 'Technologie',      'mooc' => 75],
        ];
    }

    /** Campagnes (CAMPAIGN). */
    public function campaigns(): array
    {
        return [
            'rentree-2026' => [
                'slug' => 'rentree-2026', 'name' => 'Rentrée sécurité routière 2026', 'territory' => 'National',
                'start' => '2026-09-01', 'end' => '2026-12-18', 'status' => 'En cours',
                'description' => 'Campagne nationale de rentrée : sensibiliser les collégiens aux trajets domicile–école.',
            ],
            'idf-mobilites-douces' => [
                'slug' => 'idf-mobilites-douces', 'name' => 'Mobilités douces en Île-de-France', 'territory' => 'Île-de-France',
                'start' => '2026-09-15', 'end' => '2026-11-30', 'status' => 'En cours',
                'description' => 'Vélo, trottinette et piétons : partage de la route en milieu urbain dense.',
            ],
            'hdf-deux-roues' => [
                'slug' => 'hdf-deux-roues', 'name' => 'Deux-roues motorisés — Hauts-de-France', 'territory' => 'Hauts-de-France',
                'start' => '2026-10-01', 'end' => '2027-01-31', 'status' => 'En cours',
                'description' => 'Prévention auprès des lycéens : équipements, vitesse et vulnérabilité des deux-roues.',
            ],
            'printemps-2026' => [
                'slug' => 'printemps-2026', 'name' => 'Printemps de la prévention 2026', 'territory' => 'National',
                'start' => '2026-03-02', 'end' => '2026-06-26', 'status' => 'Terminée',
                'description' => 'Édition pilote de la phase 2, avant le déploiement de la plateforme.',
            ],
        ];
    }

    // ------------------------------------------------------------------
    // Sessions pédagogiques (SESSION + agrégats GAME_RESULT)
    // ------------------------------------------------------------------

    /**
     * Sessions : code, enseignant, classe, date, campagne, parcours, statut, participants, parcours terminés.
     * Les taux de réussite par thématique sont des agrégats de session (jamais par élève).
     */
    public function sessions(): array
    {
        static $sessions = null;
        if ($sessions !== null) {
            return $sessions;
        }

        // code, enseignant, classe, niveau, date, campagne, parcours, statut, participants, terminés
        $raw = [
            ['SR-5832', 'T1',  '3e B',   '3e',   '2026-09-29 10:00', 'rentree-2026',         'Trajet domicile–collège', 'planifiee', 0,  0],
            ['SR-5790', 'T1',  '3e A',   '3e',   '2026-09-17 14:00', 'rentree-2026',         'Trajet domicile–collège', 'terminee',  27, 24],
            ['SR-5711', 'T1',  '4e C',   '4e',   '2026-09-10 09:00', 'idf-mobilites-douces', 'Vélo & trottinette',      'terminee',  26, 25],
            ['SR-5654', 'T1',  '3e B',   '3e',   '2026-09-03 11:00', 'rentree-2026',         'Découverte',              'terminee',  25, 21],
            ['SR-5801', 'T2',  '5e A',   '5e',   '2026-09-18 10:00', 'rentree-2026',         'Découverte',              'terminee',  28, 27],
            ['SR-5822', 'T3',  '4e A',   '4e',   '2026-09-22 15:00', 'idf-mobilites-douces', 'Vélo & trottinette',      'terminee',  24, 20],
            ['SR-5840', 'T2',  '5e B',   '5e',   '2026-10-02 10:00', 'rentree-2026',         'Découverte',              'planifiee', 0,  0],
            ['SR-5602', 'T5',  '2nde 3', '2nde', '2026-09-08 13:30', 'idf-mobilites-douces', 'Vélo & trottinette',      'terminee',  32, 30],
            ['SR-5745', 'T6',  '3e D',   '3e',   '2026-09-15 09:00', 'rentree-2026',         'Trajet domicile–collège', 'terminee',  23, 18],
            ['SR-5760', 'T7',  '4e B',   '4e',   '2026-09-16 10:30', 'rentree-2026',         'Trajet domicile–collège', 'terminee',  27, 26],
            ['SR-5812', 'T8',  '1re STI','1re',  '2026-09-21 08:30', 'rentree-2026',         'Deux-roues',              'terminee',  30, 25],
            ['SR-5830', 'T9',  '3e C',   '3e',   '2026-09-23 14:00', 'rentree-2026',         'Trajet domicile–collège', 'en_cours',  26, 0],
            ['SR-5855', 'T10', '2nde 1', '2nde', '2026-10-05 09:00', 'hdf-deux-roues',       'Deux-roues',              'planifiee', 0,  0],
            ['SR-5777', 'T10', '1re 2',  '1re',  '2026-09-17 10:00', 'rentree-2026',         'Deux-roues',              'terminee',  31, 28],
            ['SR-5690', 'T11', '5e C',   '5e',   '2026-09-09 15:00', 'rentree-2026',         'Découverte',              'terminee',  25, 24],
            ['SR-5733', 'T12', '2nde 4', '2nde', '2026-09-14 11:00', 'rentree-2026',         'Deux-roues',              'terminee',  29, 23],
            ['SR-5808', 'T5',  '1re 1',  '1re',  '2026-09-19 14:00', 'idf-mobilites-douces', 'Vélo & trottinette',      'terminee',  33, 31],
            ['SR-5819', 'T7',  '3e A',   '3e',   '2026-09-22 09:00', 'rentree-2026',         'Trajet domicile–collège', 'terminee',  28, 27],
        ];

        // Taux de réussite par thématique imposés pour les sessions de l'exemple (§8 de la note)
        $fixed = [
            'SR-5790' => ['signalisation' => 84, 'priorites' => 76, 'vitesse' => 71, 'distraction' => 59, 'alcool' => 73, 'vulnerables' => 68, 'equipements' => 80],
        ];

        $statuses = ['planifiee' => 'Planifiée', 'en_cours' => 'En cours', 'terminee' => 'Terminée'];
        $teachers = $this->teachers();

        $sessions = [];
        foreach ($raw as [$code, $teacher, $class, $level, $date, $campaign, $path, $status, $participants, $completed]) {
            $rates = [];
            if ($status === 'terminee') {
                // Valeurs déterministes (même code => mêmes chiffres à chaque affichage)
                mt_srand(crc32($code));
                foreach (array_keys($this->themes()) as $theme) {
                    $rates[$theme] = $fixed[$code][$theme] ?? mt_rand(52, 90);
                }
            }

            $sessions[$code] = [
                'code'          => $code,
                'teacher'       => $teacher,
                'establishment' => $teachers[$teacher]['establishment'],
                'class'         => $class,
                'level'         => $level,
                'date'          => new \DateTimeImmutable($date),
                'campaign'      => $campaign,
                'path'          => $path,
                'status'        => $status,
                'statusLabel'   => $statuses[$status],
                'participants'  => $participants,
                'completed'     => $completed,
                'completion'    => $participants ? (int) round($completed / $participants * 100) : null,
                'rates'         => $rates,
                'success'       => $rates ? (int) round(array_sum($rates) / count($rates)) : null,
                'avgMinutes'    => $status === 'terminee' ? 34 + (crc32($code) % 14) : null,
            ];
        }

        uasort($sessions, fn (array $a, array $b) => $b['date'] <=> $a['date']);

        return $sessions;
    }

    /** Sessions filtrées (enseignant, établissement, campagne, région, département, niveau). */
    public function sessionsWhere(array $criteria): array
    {
        $establishments = $this->establishments();

        return array_filter($this->sessions(), function (array $s) use ($criteria, $establishments) {
            $e = $establishments[$s['establishment']];
            return (empty($criteria['teacher'])       || $s['teacher'] === $criteria['teacher'])
                && (empty($criteria['establishment']) || $s['establishment'] === $criteria['establishment'])
                && (empty($criteria['campaign'])      || $s['campaign'] === $criteria['campaign'])
                && (empty($criteria['region'])        || $e['region'] === $criteria['region'])
                && (empty($criteria['dept'])          || $e['dept'] === (string) $criteria['dept'])
                && (empty($criteria['level'])         || $s['level'] === $criteria['level'])
                && (empty($criteria['from'])          || $s['date'] >= new \DateTimeImmutable($criteria['from']))
                && (empty($criteria['to'])            || $s['date'] <= new \DateTimeImmutable($criteria['to'] . ' 23:59'));
        });
    }

    /**
     * Agrégats d'un ensemble de sessions : volumes, complétion, réussite, réussite par thématique.
     * Point fort / point d'attention = meilleure / plus faible thématique.
     */
    public function aggregate(array $sessions): array
    {
        $done = array_filter($sessions, fn (array $s) => $s['status'] === 'terminee');
        $participants = array_sum(array_column($done, 'participants'));
        $completed    = array_sum(array_column($done, 'completed'));

        // Moyenne pondérée par le nombre de participants
        $themes = [];
        foreach ($this->themes() as $key => $label) {
            $sum = 0;
            foreach ($done as $s) {
                $sum += $s['rates'][$key] * $s['participants'];
            }
            $themes[$key] = ['key' => $key, 'label' => $label, 'rate' => $participants ? (int) round($sum / $participants) : 0];
        }
        $sorted = $themes;
        usort($sorted, fn (array $a, array $b) => $a['rate'] <=> $b['rate']);

        return [
            'sessions'     => count($sessions),
            'done'         => count($done),
            'planned'      => count(array_filter($sessions, fn (array $s) => $s['status'] === 'planifiee')),
            'participants' => $participants,
            'completed'    => $completed,
            'completion'   => $participants ? (int) round($completed / $participants * 100) : 0,
            'success'      => $themes ? (int) round(array_sum(array_column($themes, 'rate')) / count($themes)) : 0,
            'avgMinutes'   => $done ? (int) round(array_sum(array_column($done, 'avgMinutes')) / count($done)) : 0,
            'themes'       => $sorted,
            'strongest'    => $done ? end($sorted) : null,
            'weakest'      => $done ? $sorted[0] : null,
            'establishments' => count(array_unique(array_column($sessions, 'establishment'))),
            'teachers'     => count(array_unique(array_column($sessions, 'teacher'))),
        ];
    }

    /** Évolution mensuelle nationale (joueurs), pour le cockpit de pilotage. */
    public function monthlyPlayers(): array
    {
        return [
            ['month' => '2026-01', 'label' => 'janv.', 'players' => 1240],
            ['month' => '2026-02', 'label' => 'févr.', 'players' => 1580],
            ['month' => '2026-03', 'label' => 'mars',  'players' => 2960],
            ['month' => '2026-04', 'label' => 'avr.',  'players' => 3410],
            ['month' => '2026-05', 'label' => 'mai',   'players' => 3870],
            ['month' => '2026-06', 'label' => 'juin',  'players' => 2150],
            ['month' => '2026-07', 'label' => 'juil.', 'players' => 180],
            ['month' => '2026-08', 'label' => 'août',  'players' => 95],
            ['month' => '2026-09', 'label' => 'sept.', 'players' => 4620],
        ];
    }

    /** Erreurs les plus fréquentes remontées par le jeu (agrégées). */
    public function frequentErrors(): array
    {
        return [
            ['theme' => 'distraction',   'label' => 'Traverser en regardant son téléphone',                 'share' => 41],
            ['theme' => 'priorites',     'label' => 'Refus de priorité à droite sans signalisation',         'share' => 36],
            ['theme' => 'vulnerables',   'label' => 'Angle mort d\'un poids lourd non anticipé',             'share' => 33],
            ['theme' => 'vitesse',       'label' => 'Distance de freinage sous-estimée sous la pluie',       'share' => 29],
            ['theme' => 'signalisation', 'label' => 'Confusion entre sens interdit et interdiction de circuler', 'share' => 22],
        ];
    }

    // ------------------------------------------------------------------
    // Ressources pédagogiques (PEDAGOGICAL_RESOURCE) — gérées par le mini-CMS
    // ------------------------------------------------------------------

    public function resourceUsages(): array
    {
        return [
            'preparer'    => ['label' => 'Préparer ma séance',   'hint' => 'Fiche enseignant, objectifs, déroulé 45–60 min, conseils d\'animation.'],
            'introduire'  => ['label' => 'Introduire la séance', 'hint' => 'Vidéo d\'introduction, supports de présentation, chiffres clés.'],
            'debriefer'   => ['label' => 'Débriefer',            'hint' => 'Supports de débrief, questions ouvertes, quiz, messages de prévention.'],
            'approfondir' => ['label' => 'Approfondir',          'hint' => 'Ressources thématiques, contenus complémentaires, documentation.'],
        ];
    }

    public function resourceTypes(): array
    {
        return ['fiche' => 'Fiche', 'video' => 'Vidéo', 'presentation' => 'Présentation', 'quiz' => 'Quiz', 'document' => 'Document'];
    }

    public function resources(): array
    {
        $r = [
            ['fiche-enseignant-seance', 'Fiche enseignant — déroulé d\'une séance Saferoads', 'preparer', 'fiche', ['5e', '4e', '3e'], null, null, '2026-08-28', 'PDF · 4 pages', 'Objectifs pédagogiques, déroulé minuté (45–60 min), conseils d\'animation et modalités techniques (tablettes, codes session, mode déconnecté).'],
            ['modalites-techniques', 'Modalités techniques : salle, tablettes et mode déconnecté', 'preparer', 'document', [], null, null, '2026-08-28', 'PDF · 2 pages', 'Préparer le matériel, lancer une session avec le code ou le QR Code, que faire en cas de coupure réseau.'],
            ['video-introduction', 'Vidéo d\'introduction pour la classe', 'introduire', 'video', ['5e', '4e', '3e'], null, null, '2026-09-01', 'Vidéo · 3 min 20', 'Une courte vidéo pour lancer la séance et présenter les enjeux de la sécurité routière aux élèves.'],
            ['chiffres-cles-2026', 'Chiffres clés de la sécurité routière chez les jeunes', 'introduire', 'presentation', ['3e', '2nde', '1re'], null, 'rentree-2026', '2026-09-01', 'Diaporama · 12 diapositives', 'Données de l\'accidentalité des 10–18 ans pour introduire la séance, sources ONISR.'],
            ['debrief-distraction', 'Support de débrief — Distraction et téléphone', 'debriefer', 'presentation', ['4e', '3e', '2nde'], 'distraction', null, '2026-09-05', 'Diaporama · 8 diapositives', 'Questions ouvertes, situations du jeu à rejouer et messages de prévention sur l\'usage du téléphone en marchant ou à vélo.'],
            ['debrief-priorites', 'Support de débrief — Priorités et intersections', 'debriefer', 'presentation', ['5e', '4e', '3e'], 'priorites', null, '2026-09-05', 'Diaporama · 7 diapositives', 'Revenir sur les intersections du parcours : priorité à droite, cédez-le-passage, stop.'],
            ['debrief-signalisation', 'Support de débrief — Signalisation', 'debriefer', 'presentation', ['5e', '4e', '3e'], 'signalisation', null, '2026-09-05', 'Diaporama · 6 diapositives', 'Les familles de panneaux, formes et couleurs, avec les erreurs les plus fréquentes du jeu.'],
            ['quiz-debrief-vulnerables', 'Quiz de débrief — Usagers vulnérables', 'debriefer', 'quiz', ['4e', '3e', '2nde', '1re'], 'vulnerables', 'idf-mobilites-douces', '2026-09-12', 'Quiz · 10 questions', 'Angles morts, piétons, cyclistes : un quiz collectif pour clôturer la séance.'],
            ['debrief-vitesse', 'Support de débrief — Vitesse et distance de freinage', 'debriefer', 'presentation', ['3e', '2nde', '1re'], 'vitesse', null, '2026-09-15', 'Diaporama · 7 diapositives', 'Comprendre la distance d\'arrêt, l\'effet de la pluie et du temps de réaction.'],
            ['dossier-deux-roues', 'Dossier thématique — Deux-roues motorisés', 'approfondir', 'document', ['2nde', '1re'], 'equipements', 'hdf-deux-roues', '2026-10-01', 'PDF · 16 pages', 'Équipements obligatoires, vulnérabilité, prise de risque : dossier complet pour prolonger la séance.'],
            ['alcool-stupefiants', 'Alcool, cannabis et conduite : ressources pour le lycée', 'approfondir', 'document', ['2nde', '1re'], 'alcool', null, '2026-09-20', 'PDF · 8 pages', 'Effets sur la vigilance, cadre légal, pistes de discussion avec les élèves.'],
            ['mobilites-douces-ville', 'Partager la rue : vélo, trottinette et piétons en ville', 'approfondir', 'video', ['5e', '4e', '3e'], 'vulnerables', 'idf-mobilites-douces', '2026-09-18', 'Vidéo · 6 min', 'Reportage et fiche d\'exploitation autour des mobilités douces en milieu urbain.'],
        ];

        $out = [];
        foreach ($r as [$slug, $title, $usage, $type, $levels, $theme, $campaign, $date, $format, $summary]) {
            $out[$slug] = compact('slug', 'title', 'usage', 'type', 'levels', 'theme', 'campaign', 'format', 'summary')
                + ['publishedAt' => new \DateTimeImmutable($date), 'status' => 'publiee'];
        }

        return $out;
    }

    /** Ressources filtrées (niveau, thématique, type, campagne, usage). */
    public function resourcesWhere(array $f): array
    {
        return array_filter($this->resources(), fn (array $r) =>
            (empty($f['level'])    || !$r['levels'] || in_array($f['level'], $r['levels'], true))
            && (empty($f['theme'])    || $r['theme'] === $f['theme'])
            && (empty($f['type'])     || $r['type'] === $f['type'])
            && (empty($f['campaign']) || $r['campaign'] === $f['campaign'])
            && (empty($f['usage'])    || $r['usage'] === $f['usage'])
        );
    }

    /** Support de débrief associé à une thématique (§8 : "action attendue"). */
    public function debriefFor(string $theme): ?array
    {
        foreach ($this->resources() as $r) {
            if ($r['usage'] === 'debriefer' && $r['theme'] === $theme) {
                return $r;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------
    // MOOC enseignant (MOOC_MODULE, MOOC_LESSON, QUIZ, TEACHER_PROGRESS)
    // ------------------------------------------------------------------

    public function moocModules(): array
    {
        $modules = [
            ['decouvrir',   'Découvrir Saferoads',                         'Le serious game, ses parcours et la place de la plateforme.',              ['Qu\'est-ce que Saferoads ?' => 'video', 'Les parcours et thématiques' => 'texte', 'Votre espace enseignant' => 'video'], 'done'],
            ['preparer',    'Préparer une séance',                          'Choisir un parcours, créer une session, préparer la salle.',               ['Définir ses objectifs' => 'texte', 'Obtenir un code session' => 'video', 'Check-list matériel' => 'pdf'], 'done'],
            ['utiliser',    'Utiliser le serious game en classe',          'Lancer la session, accompagner les élèves, gérer les imprévus.',            ['Lancer la session avec le QR Code' => 'video', 'Posture d\'animation' => 'texte', 'Mode déconnecté' => 'texte'], 'done'],
            ['comprendre',  'Comprendre les résultats',                     'Lire le tableau de bord de session : complétion, thématiques, points de débrief.', ['Les indicateurs de session' => 'video', 'Identifier un point d\'attention' => 'texte'], 'in_progress'],
            ['debrief',     'Animer le débrief',                            'Transformer les résultats en discussion et messages de prévention.',       ['Structurer un débrief de 15 min' => 'video', 'Questions ouvertes' => 'texte', 'Supports de débrief' => 'pdf'], 'todo'],
            ['approfondir', 'Approfondir les thématiques de sécurité routière', 'Distraction, vitesse, usagers vulnérables, alcool : repères pour l\'enseignant.', ['Distraction et écrans' => 'video', 'Usagers vulnérables' => 'video', 'Ressources complémentaires' => 'pdf'], 'todo'],
        ];

        $quiz = [
            ['q' => 'Qui génère les codes session ?', 'answers' => ['L\'enseignant, librement', 'AC, via la plateforme', 'Les élèves'], 'correct' => 1],
            ['q' => 'Que signifie un « point d\'attention » dans les résultats ?', 'answers' => ['La thématique la moins réussie de la séance', 'Un élève en difficulté', 'Une erreur technique'], 'correct' => 0],
            ['q' => 'Les résultats de session contiennent-ils des données nominatives d\'élèves ?', 'answers' => ['Oui', 'Non, uniquement des agrégats'], 'correct' => 1],
        ];

        $out = [];
        foreach ($modules as $i => [$slug, $title, $summary, $lessons, $state]) {
            $done = match ($state) { 'done' => count($lessons), 'in_progress' => 1, default => 0 };
            $l = [];
            $n = 0;
            foreach ($lessons as $name => $kind) {
                $l[] = ['title' => $name, 'kind' => $kind, 'done' => $n++ < $done, 'minutes' => $kind === 'video' ? 6 : 4];
            }
            $out[$slug] = [
                'slug' => $slug, 'number' => $i + 1, 'title' => $title, 'summary' => $summary,
                'lessons' => $l, 'state' => $state, 'quiz' => $quiz,
                'progress' => (int) round($done / count($lessons) * 100),
                'minutes' => array_sum(array_column($l, 'minutes')) + 5,
            ];
        }

        return $out;
    }

    /** Statistiques du MOOC (back-office). */
    public function moocStats(): array
    {
        $teachers = $this->teachers();
        $progress = array_column($teachers, 'mooc');

        return [
            'enrolled'  => count($teachers),
            'started'   => count(array_filter($progress, fn ($p) => $p > 0)),
            'completed' => count(array_filter($progress, fn ($p) => $p === 100)),
            'average'   => (int) round(array_sum($progress) / count($progress)),
            'quizScore' => 82,
        ];
    }

    // ------------------------------------------------------------------
    // Communication (COMMUNICATION, AUDIENCE, READ_STATUS)
    // ------------------------------------------------------------------

    /** Communications publiées par la DSR / Saferoads, avec leurs statistiques de diffusion. */
    public function communications(): array
    {
        return [
            1 => ['id' => 1, 'kind' => 'ressource', 'source' => 'Saferoads', 'title' => 'Nouvelle ressource : support de débrief « Distraction »',
                  'body' => "Un nouveau support de débrief est disponible dans le centre de ressources. Il s'appuie sur les situations du jeu où les élèves utilisent leur téléphone en se déplaçant.\n\nIl est conçu pour une discussion de 15 minutes en fin de séance.",
                  'date' => '2026-09-21', 'audience' => 'Tous les enseignants', 'recipients' => 428, 'reads' => 312, 'status' => 'publiee', 'link' => ['route' => 'resources_show', 'slug' => 'debrief-distraction']],
            2 => ['id' => 2, 'kind' => 'message', 'source' => 'DSR', 'title' => 'Lancement de la campagne « Rentrée sécurité routière 2026 »',
                  'body' => "La campagne nationale de rentrée est ouverte jusqu'au 18 décembre. Les sessions rattachées à la campagne alimenteront le bilan national présenté en janvier.\n\nMerci à tous les enseignants mobilisés.",
                  'date' => '2026-09-01', 'audience' => 'Tous les enseignants', 'recipients' => 428, 'reads' => 377, 'status' => 'publiee', 'link' => null],
            3 => ['id' => 3, 'kind' => 'module', 'source' => 'Saferoads', 'title' => 'Nouveau module MOOC : « Animer le débrief »',
                  'body' => "Le module 5 du MOOC est en ligne : structurer un débrief, poser des questions ouvertes et utiliser les supports proposés.\n\nDurée : environ 20 minutes.",
                  'date' => '2026-09-14', 'audience' => 'Enseignants inscrits au MOOC', 'recipients' => 296, 'reads' => 188, 'status' => 'publiee', 'link' => ['route' => 'mooc_module', 'slug' => 'debrief']],
            4 => ['id' => 4, 'kind' => 'message', 'source' => 'DSR', 'title' => 'Île-de-France : semaine des mobilités douces',
                  'body' => "Du 13 au 17 octobre, les établissements franciliens sont invités à programmer une séance sur le parcours « Vélo & trottinette ».",
                  'date' => '2026-09-24', 'audience' => 'Région Île-de-France', 'recipients' => 134, 'reads' => 0, 'status' => 'planifiee', 'link' => null],
        ];
    }

    /** Messages reçus par l'enseignant connecté, avec statut lu / non lu (stocké en session). */
    public function inbox(): array
    {
        $read = $this->readIds();
        $messages = array_filter($this->communications(), fn (array $c) => $c['status'] === 'publiee');
        foreach ($messages as $id => $m) {
            // Le message n°2 est déjà lu dans la démo
            $messages[$id]['read'] = $id === 2 || in_array($id, $read, true);
        }
        uasort($messages, fn (array $a, array $b) => $b['date'] <=> $a['date']);

        return $messages;
    }

    public function unreadCount(): int
    {
        return count(array_filter($this->inbox(), fn (array $m) => !$m['read']));
    }

    public function markRead(int $id): void
    {
        $session = $this->requestStack->getSession();
        $session->set('read_messages', array_values(array_unique([...$this->readIds(), $id])));
    }

    private function readIds(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request && $request->hasSession() ? $request->getSession()->get('read_messages', []) : [];
    }

    /** Ciblages possibles d'une communication (§19). */
    public function audiences(): array
    {
        return [
            'all'           => 'Tous les enseignants',
            'region'        => 'Région',
            'department'    => 'Département',
            'level'         => 'Niveau scolaire',
            'campaign'      => 'Campagne',
            'establishment' => 'Établissement',
        ];
    }

    // ------------------------------------------------------------------
    // Rôles et permissions (ROLE, PERMISSION)
    // ------------------------------------------------------------------

    public function permissions(): array
    {
        //                                             Ens.  Chef  DSR   Admin
        return [
            'Ses propres sessions et résultats'      => [true,  false, false, false],
            'Ressources pédagogiques et MOOC'         => [true,  true,  false, true],
            'Centre de messages'                      => [true,  true,  false, false],
            'Suivi de son établissement'              => [false, true,  false, false],
            'Cockpit statistique global'              => [false, false, true,  true],
            'Lecture territoriale et campagnes'       => [false, false, true,  true],
            'Reporting et exports'                    => [false, false, true,  true],
            'Console de communication'                => [false, false, true,  true],
            'Back-office (gestion, rôles, CMS, MOOC)' => [false, false, false, true],
            'Données nominatives élèves'              => [false, false, false, false],
        ];
    }
}
