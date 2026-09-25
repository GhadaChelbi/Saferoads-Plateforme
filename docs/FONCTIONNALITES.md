# Plateforme Saferoads — Fonctionnalités du site web

La plateforme web est le **portail professionnel** du dispositif Saferoads. Elle est distincte du serious game : l'expérience du joueur (profil, avatar, scores, badges) reste dans le jeu. Le site sert aux adultes qui organisent, suivent et pilotent le dispositif.

Elle couvre quatre fonctions :

| Bloc | Rôle |
|---|---|
| **Sessions** | Utiliser Saferoads en classe : codes session, QR Codes, résultats |
| **Pédagogie** | Former et outiller les enseignants : ressources, MOOC, supports de débrief |
| **Pilotage** | Mesurer l'impact : indicateurs, territoires, campagnes, exports |
| **Communication** | Diffuser des informations de la DSR / Saferoads vers les enseignants |

> **Version actuelle : démonstration.** Toutes les données sont fictives et statiques (aucune base de données). Les formulaires se valident mais n'enregistrent rien.

---

## Sommaire

1. [Accès et profils](#1-accès-et-profils)
2. [Page d'accueil publique](#2-page-daccueil-publique)
3. [Espace enseignant](#3-espace-enseignant)
4. [Ressources, formation et messages](#4-ressources-formation-et-messages)
5. [Espace chef d'établissement](#5-espace-chef-détablissement)
6. [Cockpit de pilotage Saferoads / DSR](#6-cockpit-de-pilotage-saferoads--dsr)
7. [Console de communication](#7-console-de-communication)
8. [Back-office](#8-back-office)
9. [Confidentialité et RGPD](#9-confidentialité-et-rgpd)
10. [Limites de la version de démonstration](#10-limites-de-la-version-de-démonstration)

---

## 1. Accès et profils

On se connecte depuis **Se connecter** (`/connexion`) avec un compte professionnel. Après connexion, chaque profil arrive directement sur son propre espace.

Le formulaire vérifie que l'adresse e-mail est valide et que le mot de passe est renseigné. Après 5 échecs, les tentatives sont temporairement bloquées.

### Comptes de démonstration

Mot de passe commun : `demo1234`. Sur la page de connexion, le menu **Comptes de démonstration** remplit le formulaire en un clic.

| Compte | Profil | Arrive sur |
|---|---|---|
| `enseignant@saferoads.fr` | Enseignante — Claire Martin, Collège Jean Moulin | Tableau de bord enseignant |
| `chef@saferoads.fr` | Chef d'établissement — Collège Jean Moulin | Vue de l'établissement |
| `dsr@saferoads.fr` | DSR | Cockpit de pilotage |
| `admin@saferoads.fr` | Admin Saferoads | Back-office |

### Qui voit quoi

Chaque profil ne voit que le périmètre strictement nécessaire à sa fonction.

| Fonctionnalité | Enseignant | Chef d'établ. | DSR | Admin |
|---|:-:|:-:|:-:|:-:|
| Ses propres sessions et résultats | ✓ | | | |
| Ressources pédagogiques et MOOC | ✓ | ✓ | | ✓ |
| Centre de messages | ✓ | ✓ | | |
| Suivi de son établissement | | ✓ | | |
| Cockpit statistique global | | | ✓ | ✓ |
| Lecture territoriale et campagnes | | | ✓ | ✓ |
| Reporting et exports | | | ✓ | ✓ |
| Console de communication | | | ✓ | ✓ |
| Back-office | | | | ✓ |
| Données nominatives d'élèves | | | | |

Les droits DSR ne sont pas ceux de l'Admin : la DSR n'accède pas au back-office. Un utilisateur qui tente d'ouvrir une page hors de son périmètre reçoit un refus d'accès. Un enseignant qui ouvre la session d'un collègue obtient « session introuvable ».

---

## 2. Page d'accueil publique

Adresse : `/`. Accessible sans connexion.

- **Bannière** : présentation du portail, accès à l'espace professionnel, trois chiffres nationaux (établissements, élèves sensibilisés, taux de complétion) et une maquette animée du serious game.
- **La plateforme** : les quatre fonctions (sessions, pédagogie, pilotage, communication).
- **Pour qui ?** : ce que voit chaque profil, et ce qu'il ne voit pas.
- **Fonctionnement** : le parcours de la donnée, du jeu jusqu'à la mesure de l'impact.
- **Chiffres clés** : sessions réalisées, élèves sensibilisés, enseignants engagés, taux de complétion.

La page n'affiche **aucun classement** d'établissements et **aucune donnée de joueur** : uniquement des totaux nationaux.

---

## 3. Espace enseignant

L'enseignant ne voit **que ses propres classes et sessions**. Ce n'est pas un tableau de bord statistique global : il sert à préparer et débriefer ses séances.

### Tableau de bord (`/enseignant`)

- **Ma prochaine séance** : classe, parcours, date, code session en grand et bouton **Afficher le QR Code**.
- **Chiffres** : sessions réalisées, participants, taux de complétion, progression dans le MOOC.
- **Résultats de mes classes** : réussite par thématique, avec la **thématique forte**, le **point d'attention** et un lien direct vers le **support de débrief conseillé**.
- **Mes dernières sessions**, **Ma formation** (module à reprendre) et **Messages** récents (les non lus sont signalés).

### Mes sessions (`/enseignant/sessions`)

Liste de toutes ses sessions : code, classe, date, parcours, campagne, participants, complétion et statut (*Planifiée*, *En cours*, *Terminée*). Des filtres permettent d'afficher un seul statut.

### Détail d'une session (`/enseignant/sessions/SR-xxxx`)

- **QR Code** que les élèves scannent dans Saferoads, avec le code à saisir. Le bouton **Plein écran** sert à le projeter en classe.
- **Session terminée** : les résultats de la séance, immédiatement exploitables :
  - participants, parcours terminés, taux de complétion, réussite moyenne, temps moyen ;
  - réussite par thématique ;
  - thématique forte et point d'attention ;
  - **action attendue** : accès direct au support de débrief de la thématique la moins réussie.
- **Session planifiée ou en cours** : les ressources pour préparer la séance. Les résultats s'affichent après synchronisation des données du jeu, y compris après un passage en mode déconnecté.

> Les codes session sont générés par AC. La plateforme les affiche et les rattache aux classes, elle ne les crée pas.

### Mes résultats (`/enseignant/resultats`)

Synthèse de toutes ses classes (complétion, réussite, thématiques), puis détail **par classe** avec un accès à chaque session.

---

## 4. Ressources, formation et messages

Ces pages sont accessibles aux enseignants et aux chefs d'établissement.

### Centre de ressources (`/ressources`)

Bibliothèque classée par usage :

| Usage | Contenu |
|---|---|
| **Préparer ma séance** | Fiche enseignant, objectifs, déroulé 45–60 min, modalités techniques |
| **Introduire la séance** | Vidéo d'introduction, supports de présentation, chiffres clés |
| **Débriefer** | Supports de débrief, questions ouvertes, quiz, messages de prévention |
| **Approfondir** | Dossiers thématiques, contenus complémentaires |

Filtres : **niveau**, **thématique**, **type** (fiche, vidéo, présentation, quiz, document) et **campagne**. La liste se met à jour dès qu'un filtre change.

Chaque ressource a sa page : présentation, informations (usage, niveaux, thématique, campagne), bouton de téléchargement et ressources liées.

### Formation MOOC (`/formation`)

Formation des enseignants à l'utilisation du serious game et au débrief, en 6 modules :

1. Découvrir Saferoads
2. Préparer une séance
3. Utiliser le serious game en classe
4. Comprendre les résultats
5. Animer le débrief
6. Approfondir les thématiques de sécurité routière

- **Catalogue** : progression globale, modules terminés, en cours ou à commencer, et bouton **Reprendre** sur le module en cours.
- **Page d'un module** : liste des leçons (vidéo, texte, PDF), contenu de la leçon et **quiz de validation**. Le quiz corrige les réponses et valide le module à partir de 2 bonnes réponses sur 3.
- L'attestation de fin de formation est prévue à 100 % de progression.

### Messages & actualités (`/messages`)

Communications reçues de la DSR et de Saferoads : messages, nouvelles ressources, nouveaux modules du MOOC.

- Chaque message est marqué **Nouveau** ou **Lu**. Le nombre de non lus s'affiche dans le menu.
- Ouvrir un message le marque comme lu et donne accès à la ressource ou au module associé.

---

## 5. Espace chef d'établissement

Le chef d'établissement voit **uniquement son établissement** : pas d'autres établissements, pas de statistiques territoriales ou nationales.

### Vue d'ensemble (`/etablissement`)

- **Chiffres** : enseignants utilisateurs, sessions réalisées, élèves participants, taux de complétion.
- **Enseignants utilisant Saferoads** : sessions, dernière séance, progression MOOC et état (actif ou pas encore de session).
- **Sessions par niveau**.
- **Résultats pédagogiques agrégés** de l'établissement, sans comparaison avec d'autres.
- **Prochaines séances** et **informations** reçues.

### Sessions (`/etablissement/sessions`)

Toutes les sessions de l'établissement : classe, enseignant, date, campagne, participants, complétion, réussite et statut, avec filtres par statut.

---

## 6. Cockpit de pilotage Saferoads / DSR

Réservé à Saferoads et à la DSR. Toutes les statistiques sont des **agrégats**, sans donnée nominative.

### Cockpit (`/pilotage`)

**Filtres** : période (du / au), campagne, région, département, établissement, niveau scolaire et thématique.

**Lecture territoriale** : navigation par niveaux **France › Région › Département › Établissement › Session**. Le lien **Détailler** descend d'un niveau, et le fil d'Ariane permet de remonter.

**Indicateurs** :
- joueurs, sessions, établissements, enseignants ;
- taux de complétion et taux de réussite (ou réussite de la thématique filtrée) ;
- évolution du nombre de joueurs dans le temps (graphique interactif, avec une vue tableau des données) ;
- réussite par thématique ;
- erreurs les plus fréquentes.

> Les listes sont triées par **ordre alphabétique** : la plateforme ne crée **pas de classement** des établissements.

### Établissements (`/pilotage/etablissements`)

Pour chaque établissement : commune, département, niveaux concernés, sessions, participants, complétion et réussite, avec un lien vers le cockpit filtré sur l'établissement.

### Campagnes (`/pilotage/campagnes`)

Pour chaque campagne : territoire, période, statut, établissements participants, sessions, volume de joueurs et complétion. La **page d'une campagne** ajoute la répartition par région et les résultats par thématique.

### Reporting & exports (`/pilotage/reporting`)

- Aperçu des sessions correspondant aux filtres choisis.
- **Export CSV** (compatible Excel, accents conservés) et **Export Excel (.xlsx)**.
- Les exports **tiennent compte des filtres** appliqués. Exemple : septembre–décembre, Île-de-France, niveau 3e, puis export.
- Une ligne par session, avec des agrégats uniquement : code, date, établissement, territoire, niveau, campagne, participants, complétion, réussite, temps moyen et réussite par thématique.

---

## 7. Console de communication

Réservée à la DSR et aux administrateurs autorisés. Elle sert à communiquer avec les **enseignants inscrits**, jamais avec les élèves. Il s'agit de publications institutionnelles, pas d'une messagerie instantanée.

### Historique (`/communication`)

- Nombre de communications, envois et taux de consultation global.
- Pour chaque communication : émetteur, date, destinataires et **suivi de lecture**. Exemple : *disponible pour 428 enseignants, consultée par 312*.
- Les communications planifiées sont signalées.

### Nouvelle communication (`/communication/nouvelle`)

| Champ | Détail |
|---|---|
| Titre | Objet de la communication (obligatoire) |
| Contenu | Texte éditorial (obligatoire) |
| Pièce jointe | Fichier (PDF, présentation, image…) |
| Lien interne | Vers une ressource ou un module du MOOC |
| Destinataires | Tous les enseignants, ou un segment : région, département, niveau, campagne, établissement |
| Date de publication | Immédiate ou planifiée |
| E-mail | Option d'envoi d'un e-mail professionnel de notification |

---

## 8. Back-office

Réservé à l'Admin Saferoads (`/admin`).

### Gestion

| Section | Contenu |
|---|---|
| Enseignants | Nom, e-mail professionnel, établissement, discipline, progression MOOC |
| Établissements | Type, commune, département, région |
| Sessions | Code, date, enseignant, établissement, niveau, statut (codes générés par AC) |
| Campagnes | Territoire, dates, statut |
| Ressources (mini-CMS) | Liste et **formulaire d'ajout** |
| Communications | Liste avec émetteur, destinataires et consultation |

Chaque liste a une **recherche instantanée**.

### Mini-CMS des ressources (`/admin/ressources/nouvelle`)

Ajouter une ressource sans intervention du développeur : titre, présentation, fichier joint, vidéo (URL), catégorie (usage), type, niveaux, thématique, campagne, date de publication et statut (brouillon ou publiée).

### Administration du MOOC (`/admin/mooc`)

- Statistiques de formation : enseignants inscrits, démarrages, formations terminées, progression moyenne, résultat moyen aux quiz.
- Liste des modules avec leurs leçons et quiz, **réordonnables** avec les flèches ↑ ↓.

### Rôles & permissions (`/admin/roles`)

Tableau des droits de chaque profil, rappel des comptes de démonstration et des principes RGPD.

### Qualité des données (`/admin/qualite`)

Suivi du traitement des données envoyées par le jeu : événements reçus, rejetés (source non authentifiée, format invalide), doublons écartés après resynchronisation, sessions en attente de synchronisation. La chaîne de traitement est présentée étape par étape :

**Recevoir → Authentifier la source → Valider → Contrôler → Dédoublonner → Stocker → Agréger → Restituer**

---

## 9. Confidentialité et RGPD

- **Aucune base nominative d'élèves** : les résultats sont des agrégats par session.
- **Minimisation** : seules les données nécessaires à la séance, à l'analyse, au pilotage et au reporting sont utilisées.
- **Comptes professionnels** pour les enseignants et les administrateurs, avec gestion des rôles.
- **Cloisonnement** : chaque profil ne voit que son périmètre (voir [Qui voit quoi](#qui-voit-quoi)).
- **Pas de classement public** des établissements.
- Hébergement en France prévu. Les durées de conservation restent à définir.

---

## 10. Limites de la version de démonstration

| Élément | État actuel |
|---|---|
| Données | Fictives et statiques, identiques à chaque affichage |
| Comptes | 4 comptes de démonstration, sans inscription possible |
| Formulaires (communication, ressource) | Validés, mais non enregistrés |
| Modification et publication dans le back-office | Boutons présents, désactivés |
| Statut « lu » des messages | Conservé pendant la session de navigation uniquement |
| Téléchargement des ressources, vidéos | Aperçus, sans fichier réel |
| Données du jeu, qualité des données | Illustratives : le contrat de données reste à définir avec l'équipe du jeu |
| Adresse du QR Code | Provisoire, à confirmer avec l'équipe du jeu |

Pour lancer le site en local, voir la section suivante.

### Lancer le site

Prérequis : PHP 8.2 ou plus (extensions `intl`, `gd`, `zip`), Composer et le CLI Symfony.

```bash
composer install
symfony serve
```

Puis ouvrir http://127.0.0.1:8000.
