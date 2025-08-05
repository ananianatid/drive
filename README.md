# Projet Vite + Laravel + TailwindCSS

Ce projet utilise [Vite](https://vitejs.dev/), [Laravel](https://laravel.com/) et [TailwindCSS](https://tailwindcss.com/) pour un développement web moderne et rapide.

## Installation

1. **Cloner le dépôt :**
   ```bash
   git clone https://github.com/ananianatid/drive.git
   cd drive
   ```

2. **Installer les dépendances PHP et JavaScript :**
   ```bash
   composer install
   npm install
   ```

3. **Configurer l'environnement :**
   - Copier le fichier `.env.example` en `.env` et adapter les variables selon vos besoins.
   - Générer la clé d'application :
     ```bash
     php artisan key:generate
     ```

4. **Lancer le serveur de développement :**
   - Pour Laravel :
     ```bash
     php artisan serve
     ```
   - Pour Vite :
     ```bash
     npm run dev
     ```

## Fonctionnalités

- Compilation rapide des assets avec Vite.
- Intégration de TailwindCSS pour un design moderne et réactif.
- Hot reload pour un développement fluide.

## Structure du projet

- `resources/css/app.css` : Fichier principal CSS (avec Tailwind).
- `resources/js/app.js` : Fichier principal JavaScript.
- `vite.config.js` : Configuration de Vite et des plugins.

## Personnalisation

Vous pouvez modifier la configuration de Vite dans `vite.config.js` selon vos besoins, par exemple pour ajouter d'autres plugins ou points d'entrée.

## Commandes utiles

- `npm run dev` : Démarre le serveur de développement Vite.
- `npm run build` : Compile les assets pour la production.
- `php artisan serve` : Démarre le serveur Laravel.

## Ressources

- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Vite](https://vitejs.dev/guide/)
- [Documentation TailwindCSS](https://tailwindcss.com/docs)

## Licence

Ce projet est sous licence MIT.
