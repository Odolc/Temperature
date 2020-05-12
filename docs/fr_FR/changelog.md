---
layout: default
title: Plugin Température - changelog
lang: fr_FR
pluginId: temperature
---

# Info
>***Pour rappel*** s'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de corrections de bugs mineur.

# Version 20200512
- Ajout de log supplémentaire en mode DEBUG
- Nettoyage log
- Ajout bouton pour recréer les commandes
- Modification création des commandes
- Résolution Bug cron
- Amélioration affichage V4
- Mise à jour de la doc
- Ajout widget core sur les commandes
- Correction bug enregistrement individuel de chaque équipement
- Enregistrement des équipements après chaque mise à jour

>*Info : Penser à sauvegarder chaque équipement

# Version 3.1.1
- Correction Images dans la doc

# Version 3.1
- Ajout d’un cron 30
- Amélioration de l'affichage pour le Core V4
- Possibilité de renommer les commandes
- Commande Refresh (sur la tuile, scénario etc)
- Amélioration des logs
- Correction type de generic
- Correction Bug : l'actualisation des données ne se fait plus si l'équipement est désactivé
- Nettoyage des dossiers
- Ajout une pré-alarme pour le seuil Humidex
- Reprise des niveaux Humidex
- Correction documentation

>*Important : Il faut récréer les équipements pour avoir l'ajout de la pré-alarme Humidex
>*Remarque : Il est conseillé de supprimer le plugin et ensuite le réinstaller*

# Version 3.0
- Support de PHP 7.3
- Migration vers font-awesome 5
- Migration affichage au format core V4

# Version 2.0
- Mise à jour pour compatibilité Jeedom V3

# Version 1.1
- Correction Doc