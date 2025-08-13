# TodoList - Projet 8 OpenClassrooms

Application de gestion de tâches développée avec Symfony 7.3, utilisant Docker et FrankenPHP.

## 📋 Prérequis

### Pour tous les systèmes d'exploitation

- **Docker Desktop** (version 20.10 ou supérieure)
- **Docker Compose** (version 2.0 ou supérieure)
- **Git** pour cloner le projet
- **Make** (optionnel mais recommandé pour utiliser les commandes du Makefile)

### Installation des prérequis par système

#### 🖥️ Windows

1. **Docker Desktop** : [Télécharger depuis le site officiel](https://www.docker.com/products/docker-desktop)
2. **Git** : [Télécharger Git pour Windows](https://git-scm.com/download/win)
3. **Make** (optionnel) :
   - Via Chocolatey : `choco install make`
   - Via Windows Subsystem for Linux (WSL2) recommandé pour une meilleure compatibilité

#### 🍎 macOS

1. **Docker Desktop** : [Télécharger depuis le site officiel](https://www.docker.com/products/docker-desktop)
2. **Git** : Généralement déjà installé, sinon `xcode-select --install`
3. **Make** : Déjà installé avec Xcode Command Line Tools

#### 🐧 Linux (Ubuntu/Debian)

```bash
# Mise à jour du système
sudo apt update

# Installation de Docker
sudo apt install docker.io docker-compose-plugin

# Installation de Git et Make
sudo apt install git make

# Ajouter l'utilisateur au groupe docker (nécessite une reconnexion)
sudo usermod -aG docker $USER
```

## 🚀 Installation et lancement

### 1. Clonage du projet

```bash
git clone https://github.com/votre-username/TodoList.git
cd TodoList
```

### 2. Lancement avec Make (recommandé)

Si vous avez Make installé, utilisez les commandes suivantes :

```bash
# Construction et démarrage des containers
make start

# Chargement de la base de données avec les migrations et fixtures
make reset-db
```

### 3. Lancement sans Make

Si vous n'avez pas Make, utilisez directement Docker Compose :

```bash
# Construction des images Docker
docker compose build --pull --no-cache

# Démarrage des containers en arrière-plan
docker compose up --detach

# Création et migration de la base de données
docker compose exec php php bin/console doctrine:database:create --if-not-exists
docker compose exec php php bin/console doctrine:migrations:migrate -n

# Chargement des fixtures (données de test)
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

## 🌐 Accès à l'application

Une fois les containers démarrés, l'application est accessible à :

- **HTTP** : [http://localhost](http://localhost) ou [http://localhost:80](http://localhost:80)
- **HTTPS** : [https://localhost](https://localhost) ou [https://localhost:443](https://localhost:443)

## 🛠️ Commandes utiles

### Gestion des containers

```bash
# Voir les logs en temps réel
make logs
# ou : docker compose logs --tail=0 --follow

# Arrêter les containers
make down
# ou : docker compose down --remove-orphans

# Redémarrer complètement
make start
# ou : docker compose down && docker compose up --detach
```

### Accès au container PHP

```bash
# Shell simple
make sh
# ou : docker compose exec php sh

# Bash (avec historique des commandes)
make bash
# ou : docker compose exec php bash
```

### Gestion de la base de données

```bash
# Réinitialiser complètement la base de données
make reset-db

# Créer une nouvelle migration
make migration
# ou : docker compose exec php php bin/console make:migration

# Appliquer les migrations
make migrate
# ou : docker compose exec php php bin/console doctrine:migrations:migrate
```

### Développement Symfony

```bash
# Créer une nouvelle entité
make entity
# ou : docker compose exec php php bin/console make:entity

# Créer un nouveau contrôleur
make controller
# ou : docker compose exec php php bin/console make:controller

# Vider le cache
make cc
# ou : docker compose exec php php bin/console cache:clear
```

### Tests

```bash
# Préparer la base de données de test
make load-db-test

# Lancer tous les tests
make test
# ou : docker compose exec php php bin/phpunit

# Lancer les tests avec couverture de code
make test-coverage
# ou : docker compose exec php php bin/phpunit --coverage-html ./coverage
```

## 🐛 Résolution des problèmes courants

### Problèmes de permissions (Linux/macOS)

```bash
# Corriger les permissions des fichiers
make permissions
# ou : sudo chmod -R 777 ./
```

### Port déjà utilisé

Si le port 80 ou 443 est déjà utilisé, vous pouvez modifier les ports dans un fichier `.env` :

```bash
# Créer un fichier .env à la racine du projet
echo "HTTP_PORT=8080" >> .env
echo "HTTPS_PORT=8443" >> .env
```

### Container qui ne démarre pas

```bash
# Vérifier les logs pour identifier le problème
docker compose logs

# Reconstruire les images sans cache
docker compose build --no-cache

# Supprimer tous les containers et volumes pour repartir de zéro
docker compose down -v
docker system prune -a
```

### Problème de base de données

```bash
# Réinitialiser complètement la base de données
make reset-db

# Ou manuellement :
docker compose exec php php bin/console doctrine:database:drop --force
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate -n
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

## 📁 Structure du projet

```
├── src/                    # Code source Symfony
│   ├── Controller/         # Contrôleurs
│   ├── Entity/            # Entités Doctrine
│   ├── Repository/        # Repositories
│   └── ...
├── templates/             # Templates Twig
├── tests/                # Tests PHPUnit
├── docker-compose.yml    # Configuration Docker
├── Dockerfile           # Image Docker personnalisée
├── Makefile            # Commandes Make
└── README.md          # Ce fichier
```

## 🔧 Configuration

### Variables d'environnement

Les principales variables d'environnement peuvent être surchargées via un fichier `.env.local` :

```bash
# Base de données
POSTGRES_DB=todolist
POSTGRES_USER=app
POSTGRES_PASSWORD=monmotdepasse

# Ports
HTTP_PORT=80
HTTPS_PORT=443
```

### Mode développement vs production

- Par défaut, le projet est configuré en mode développement
- Pour la production, utilisez le fichier `compose.prod.yaml`

## 🤝 Contribution

1. Forkez le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout de la nouvelle fonctionnalité'`)
4. Poussez vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créez une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

**Projet réalisé dans le cadre de la formation Développeur PHP/Symfony d'OpenClassrooms**
