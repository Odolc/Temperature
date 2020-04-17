---
title: Jeedom | Plugin Température
description: Plugin pour calculer le windchill et l'indice de température", Indiquer des équipements température, humidité relative et vitesse du vent. Indiquer un seuil d'alerte pour l'indice de température (40°C par défaut). Si les conditions météo de température et d'humidité s'y prêtent, le plugin calcul le Windchill et l'indice de température.
---

# Info
>*Important : en cas de mise à jour disponible pour laquelle il n’y a pas d’information dans cette section, c’est qu’elle n’intègre aucune nouveauté majeure. Cela peut être un ajout de documentation, une correction de documentation, des traductions ou bien de la correction de bugs mineurs.*

# Version 202004xx
- Ajout de log supplémentaire en mode DEBUG
- Nettoyage log
- Ajout bouton pour recréer les commandes
- Modification création des commandes
- Résolution Bug cron
- Amélioration affichage V4
- Mise à jour de la doc
- Ajout widget core sur les commandes

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