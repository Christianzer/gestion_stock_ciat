<p align="center">Gestion des messages entre Secretaire pincipal et etudiant GMSE</p>

## Information GMSE

GMSE est une application de gestion de messages.Il s'agit d'envoyer des messages aux etudiants via sms ou email.

## Installaion du projet 

##### Deploiement sur votre serveur local
- cloner le projet et se déplacer dans le dossier du répertoire
 ```sh
git clone https://github.com/Christianzer/projet_mobile_multimedia.git 
cd  projet_mobile_multimedia
 ```
- installer les dependances
```sh
composer install
```
- copier le fichier .env.example dan .env apres avoir cree ce fichier
- linux
 ```sh
cp .env.example .env
 ```
- windows
 ```sh
copy .env.example .env
 ```
 - installer la cle laravel 
 ```sh
php artisan key:generate
 ```

- installation de la bd
aller dans .env et faire la configuration de votre bd
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<nombd>
DB_USERNAME=<nom_utilisateur>
DB_PASSWORD=<mot_de_passe>

- installer la bd
 ```sh
php artisan migrate
 ```

- Etant donne que le projet est en Laravel Et Vue-js 
- Installer le dep vue
 ```sh
npm install
 ```

- Compiler vue apres avoir fini d'installer les deo
 ```sh
npm run dev
 ```

- Lancer maintenant le projet
 ```sh
php artisan serve
 ```
