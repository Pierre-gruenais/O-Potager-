# React + TypeScript + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react/README.md) uses [Babel](https://babeljs.io/) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

## Expanding the ESLint configuration

If you are developing a production application, we recommend updating the configuration to enable type aware lint rules:

- Configure the top-level `parserOptions` property like this:

```js
   parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
    project: ['./tsconfig.json', './tsconfig.node.json'],
    tsconfigRootDir: __dirname,
   },
```

- Replace `plugin:@typescript-eslint/recommended` to `plugin:@typescript-eslint/recommended-type-checked` or `plugin:@typescript-eslint/strict-type-checked`
- Optionally add `plugin:@typescript-eslint/stylistic-type-checked`
- Install [eslint-plugin-react](https://github.com/jsx-eslint/eslint-plugin-react) and add `plugin:react/recommended` & `plugin:react/jsx-runtime` to the `extends` list



<!-- # Opotager
> Ce projet est une plateforme collaboratif autour du potager. Il permet
> à deux utilisateurs de rentrer en contact, l'un pour mettre à
> diposition son terrain, l'autre qui lui permettra de faire pousser ses
> cultures.

Vous voici sur le partie back-end, entièrement fais en utilisant le langage de programmation PHP et son framework Symfony.

## Pré-requis:
 - Mariadb
 - Editeur de code (VS Code)
 - PHP
 - Composer (https://getcomposer.org/download/)
 - une interface de gestion de base de données (adminer, phpmyadmin,...)
 - Clé API du site 'unsplash' (https://unsplash.com/fr)

## SOMMAIRE

 - [Installation du projet](#installation-du-projet)
 - [A chaque modification du projet](#a-chaque-modification-du-projet)
 - [Création de compte API Unsplash](#création-de-compte-api-unsplash)

## Installation du projet

> Si vous avez déjà installer le projet, passer directement à l'étape [Mise à jour](#a-chaque-modification-du-projet).

### Cloner le dépôt
Utiliser la ligne de commande git clone + SSH du dépôt sur votre machine. (voir l'exemple si dessous)
```
git clone git@github.com:O-Clock-Vega/projet-01-le-reseau-social-du-potager-back.git
```
Utiliser ``ls`` pour vérifier sa présence.

### Installation de composer (si non installé sur votre machine)
Pour installer composer, exécuter dans votre terminal les lignes de commandes suivantes :
```
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
```
```
	 php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```
```
	php composer-setup.php
```
```
	php -r "unlink('composer-setup.php');"
```

Ensuite :
	
```
    sudo mv composer.phar /usr/local/bin/composer
```

### Configuration des variables d'environnement

Ouvrir le projet dans VS Code, depuis la racine du projet à l'aide de la commande  `code .` dans votre terminal.

Créer un fichier  `.env.local` à la racine du projet.

#### Pour la base de données :
Dans ce fichier, faites un copier coller de la ligne suivante :
```
###> doctrine/doctrine-bundle ###
    DATABASE_URL="mysql://monNom:monMdp@127.0.0.1:3306/opotager?serverVersion=mariadb-10.3.38&charset=utf8mb4"
###< doctrine/doctrine-bundle ###
```

Remplacer par vos informations, les éléments suivants:
 - monNom -> nom d'utilisateur de votre BDD
 - monMdp -> mot de passe de votre BDD
 - Vérifier votre version de mariaDB, si votre version n'est pas la 10.3.38, remplacer par la votre

> Pour connaitre votre version de mariadb, vous pouvez utiliser la
> commande `mariadb --version` dans votre terminal.

#### Configurer la clé API dans le .env.local

Afin de pouvoir générer des images via l'API Unsplash, récupérer votre clé API et suivez ces étapes. (Si vous n'avez pas de compte suivez ces [étapes](#création-de-compte-api-unsplash)

Dans  le `.env.local` ajouter la variable avec votre clé API:
```
	###> unsplashApiService ###
		unsplash_KEY=unsplash_KEY= maCléApi
	###< unsplashApiService ###
```

### Installation/mise à jour des composants Symfony

Le dossier contient un fichier nommé "composer.json". C'est dans ce fichier qu'on retrouve tous les composants nécessaire au projet pour qu'il fonctionne correctement, il vous faut donc les  installer. Dans votre terminal, à la racine du projet, taper les commandes suivantes:

```
    composer update
```

### Création de la base de données
Si tout à été configuré correctement, vous pouvez faire la commande suivante :

```
	php bin/console doctrine:database:create
```

> Si vous avez un message d'erreur, vérifier les étapes précédentes et relancer la commande. Cliquez

Vous devez normalement pouvoir voir votre base de données créer dans votre interface adminer ou phpmyadmin (sans données) à l'aide de vos identifiants renseignés précédemment.

#### Création des tables et des champs
Pour créer les tables, exécuter les commandes suivantes:
```
	php bin/console doctrine:make:migration
```
```
	php bin/console doctrine:migrations:migrate
```
Vous pouvez vérifier sur votre interface de données l'ajouts des tables et des champs.

#### Générer des fausses données
Pour générer les fixtures (fausses données) qui permette de pouvoir avoir un rendu quand votre projet est encore en DEV.

Pour les générer, utiliser la commande suivante:
```
	php bin/console doctrine:fixtures:load
```
Vous pouvez vérifier sur votre interface l'ajouts des données.

***ATTENTION: Si vous rencontrez des problèmes lors des migrations, supprimer tous les éléments dans votre BDD, supprimer les migrations dans le dossier migrations du projet et relancer les commandes à partir de l'étape [création des tables et des champs](#création-des-tables-et-des-champs).*** 


### Lancement du Server

Renseigner la commande suivante dans votre terminal, à la racine du projet :
```
	php -S localhost:8000 -t public
```

Vous avez plus qu'à utiliser dans votre projet les routes que vous avez besoin:
	exemple :`www.localhost:8000/api/garden`

## A chaque modification du projet 

### Mise à jour des composants

Dans votre terminal, récupérez les mises à jour en tapant la commande, dans le projet :
```
    git pull
```
Puis,
```
	composer updtate
```

### Si modification de la configuration de la BDD

Refaites les étapes [création des tables et des champs](#création-des-tables-et-des-champs) et 
[générer des fausses données](#générer-des-fausses-données).


***Vous pouvez relancez votre serveur !***

## Création de compte API Unsplash

###  Création du compte
 
Créer votre compte sur le site [Unsplash](https://unsplash.com/fr/join) / renseigner vos informations.
 
Une fois connecté:
	
 - Cliquer sur le menu puis dans la partie produit
 - Cliquer sur 'Développers / API'.
 - Aller sur 'Yours Apps'
 - 'New Application' -> renseigner toutes les informations demandées et valider
 - Vous devez normalement trouver votre clé API plus bas dans la page à l'endroit Keys -> Access Key
 - copier coller cette clé pour cette [étape](#configurer-la-clé-api-dans-le-.env.local) -->