# WP Ideogram API : Générer des images mises en avant sur WordPress

**Contributors:** kevinbenabdelhak  
**Tags:** image, API, featured image, bulk actions  
**Requires at least:** 5.0  
**Tested up to:** 6.6.2  
**Requires PHP:** 7.0  
**Stable tag:** 1.8   
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

Des fonctionnalités avancées pour gérer les images mises en avant en utilisant l'API Ideogram.

## Description

### WP Ideogram API - Génération Automatisée d'Images Mise en Avant

WP Ideogram API est un plugin vous permettant de générer automatiquement des images pour vos publications en utilisant l'API Ideogram. Automatisez la gestion visuelle de votre site sans effort.

[![Voir le tutoriel](https://img.youtube.com/vi/dBRef1YtBig/maxresdefault.jpg)](https://www.youtube.com/watch?v=dBRef1YtBig&ab_channel=KevinBenabdelhak)

#### Fonctionnalités principales :
1. **Clé API** : Configurez facilement votre clé API via la section des réglages.
2. **Actions Groupées** : Ajoutez un bouton d'action groupée pour générer des images mises en avant pour plusieurs publications en même temps.
3. **Communication API** : Interagissez sans problème avec l'API d'Ideogram pour générer des images en fonction des titres de vos posts.
4. **Prompt personnalisable** : Ajoutez des informations supplémentaires dans le prompt.
5. **Définissez le format** : Il y a une dizaine de formats d'image que vous pouvez sélectionner dans la page d'option.
6. **Compresser l'image** : Choisissez un taux de compression ( entre 0 et 10 ) pour réduire le poids des images.
7. **Conversion automatique** : Le plugin convertit automatiquement le PNG d'Ideogram en JPG.
8. **Remplissage des attributs** : Rempli automatiquement les valeurs des attributs alt/title des images en fonction du titre de la publication
9. **Générer une image à partir d'une sélection** : Générez des images en sélectionnant le texte d'un article (fonctionne avec l'éditeur par défaut de WordPress)
   
## Installation

1. **Téléchargez le fichier ZIP du plugin :**
   Depuis la page de WP Ideogram API, téléchargez le fichier ZIP du plugin : [https://kevin-benabdelhak.fr/plugins/wp-ideogram-api/](https://kevin-benabdelhak.fr/plugins/wp-ideogram-api/)

2. **Uploader le fichier ZIP du plugin :**
   - Allez dans le panneau d'administration de WordPress et cliquez sur "Extensions" > "Ajouter".
   - Cliquez sur "Téléverser une extension".
   - Choisissez le fichier ZIP que vous avez téléchargé et cliquez sur "Installer maintenant".

3. **Activer le plugin :**
   Une fois le plugin installé, cliquez sur "Activer".

4. **Configurer la Clé API :**
   - Allez dans "Réglages" > "WP Ideogram API".
   - Entrez votre clé API Ideogram.

## FAQ

### Comment obtenir une clé API Ideogram ?
Vous pouvez obtenir une clé API en vous inscrivant sur le site d'Ideogram et en demandant une clé via leur interface.

### Que fait exactement ce plugin avec mes publications ?
Pour chaque publication sélectionnée, le plugin envoie le titre de la publication à l'API Ideogram. L'API génère une image en fonction de ce titre, télécharge l'image avant de la définir comme image mise en avant pour la publication.

## MAJ

### 1.8
* Correction lors de la suppression d’image (la fonction dans le fichier postbox se lançait)

### 1.7
* Ajout d'un champ optionnel pour gérer le prompt d'image mise en avant dans la box d'image sur publication seule

### 1.6
* Correction lors de la selection d'un texte grâce à un double clic,  l'image ne s'intégrait pas bien dans l'éditeur
  
### 1.5 
* Correction style du bouton "générer une image" dans l'éditeur
  
### 1.4 
* Générez des images en sélectionnant du texte dans l'éditeur par défaut de WordPress
  
### 1.3
* Ajout d'un bouton de génération d'image directement dans les publications (postbox image mise en avant)

### 1.2
* Remplissage des alt/title des images générées en auto
  
### 1.1
* Conversion automatique en JPG
* Compresser l'image (de 0 à 10) et réduire son poids 

### 1.0
* Premier lancement du plugin.
* Ajout de la gestion des actions groupées pour la génération d'images mises en avant.
* Intégration de l'API Ideogram pour la génération d'images.
* Ajout d'un champ personnalisé pour le prompt.
* Sélectionner plusieurs formats d'images.
