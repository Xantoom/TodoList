# Guide de Contribution - TodoList

Merci de votre intérêt pour contribuer au projet TodoList ! Ce guide vous aidera à comprendre comment participer au développement de cette application Symfony.

## 🤝 Comment contribuer

### Types de contributions acceptées

- 🐛 **Correction de bugs** : Signalement et correction d'erreurs
- ✨ **Nouvelles fonctionnalités** : Amélioration des fonctionnalités existantes ou ajout de nouvelles
- 📚 **Documentation** : Amélioration de la documentation, du README ou des commentaires
- 🧪 **Tests** : Ajout ou amélioration des tests unitaires et fonctionnels
- 🎨 **Interface utilisateur** : Améliorations de l'UX/UI
- ⚡ **Performance** : Optimisations du code

## 🚀 Processus de contribution

### 1. Fork du projet

1. **Forkez** le dépôt depuis GitHub : [https://github.com/votre-username/TodoList](https://github.com/votre-username/TodoList)
2. **Clonez** votre fork localement :
   ```bash
   git clone https://github.com/VOTRE-USERNAME/TodoList.git
   cd TodoList
   ```

### 2. Configuration de l'environnement de développement

1. **Suivez les instructions d'installation** du [README.md](README.md)
2. **Vérifiez que l'application fonctionne** :
   ```bash
   make start
   make reset-db
   ```
3. **Accédez à l'application** : [http://localhost](http://localhost)

### 3. Création d'une branche

Créez une branche pour votre contribution :

```bash
# Pour une nouvelle fonctionnalité
git checkout -b feature/nom-de-la-fonctionnalite

# Pour une correction de bug
git checkout -b fix/description-du-bug

# Pour de la documentation
git checkout -b docs/amelioration-documentation
```

### 4. Développement

#### Standards de code

- **PHP** : Respectez les standards PSR-12
- **Symfony** : Suivez les conventions Symfony
- **Twig** : Utilisez une indentation de 4 espaces
- **JavaScript** : Utilisez des conventions ES6+

#### Bonnes pratiques

- **Tests** : Ajoutez des tests pour toute nouvelle fonctionnalité
- **Documentation** : Commentez le code complexe
- **Commits** : Utilisez des messages de commit clairs (voir section Commits)
- **Performance** : Évitez les requêtes N+1, optimisez les performances

#### Lancer les tests

```bash
# Tests unitaires et fonctionnels
make test

# Tests avec couverture de code
make test-coverage

# Tests spécifiques
docker compose exec php php bin/phpunit tests/Unit/Controller/TaskControllerTest.php
```

### 5. Commits

#### Format des messages de commit

Utilisez le format suivant pour vos commits :

```
type(scope): description courte

Description plus détaillée si nécessaire

Fixes #123
```

#### Types de commits

- `feat` : Nouvelle fonctionnalité
- `fix` : Correction de bug
- `docs` : Documentation
- `style` : Formatage, style (pas de changement de logique)
- `refactor` : Refactoring du code
- `test` : Ajout ou modification de tests
- `chore` : Maintenance, configuration

#### Exemples

```bash
git commit -m "feat(task): add task priority field

- Add priority enum (low, medium, high)
- Update task entity and form
- Add priority filter in task list

Fixes #45"

git commit -m "fix(user): resolve authentication issue

- Fix session timeout handling
- Update security configuration

Fixes #67"
```

### 6. Pull Request (PR)

#### Avant de soumettre

1. **Mettez à jour votre branche** avec la branche principale :
   ```bash
   git fetch origin
   git rebase origin/main
   ```

2. **Vérifiez que les tests passent** :
   ```bash
   make test
   ```

#### Création de la PR

1. **Poussez votre branche** :
   ```bash
   git push origin feature/nom-de-la-fonctionnalite
   ```

2. **Créez une Pull Request** sur GitHub avec :
   - Un titre descriptif
   - Une description détaillée des changements
   - Des captures d'écran si pertinentes
   - Les issues liées (`Fixes #123`)

#### Template de PR

```markdown
## Description
Brève description des changements apportés.

## Type de changement
- [ ] Bug fix
- [ ] Nouvelle fonctionnalité
- [ ] Breaking change
- [ ] Documentation

## Tests
- [ ] Tests unitaires ajoutés/mis à jour
- [ ] Tests fonctionnels ajoutés/mis à jour
- [ ] Tests manuels effectués

## Checklist
- [ ] Mon code suit les standards du projet
- [ ] J'ai effectué une auto-révision de mon code
- [ ] J'ai commenté les parties complexes
- [ ] J'ai mis à jour la documentation si nécessaire
- [ ] Mes changements ne génèrent pas de nouveaux warnings
- [ ] Les tests passent localement

## Screenshots (si applicable)
Ajoutez des captures d'écran pour illustrer les changements visuels.

## Issues liées
Fixes #123
```

## 🐛 Signaler un bug

### Avant de signaler

1. **Vérifiez** que le bug n'a pas déjà été signalé dans les [issues GitHub](https://github.com/votre-username/TodoList/issues)
2. **Reproduisez** le bug dans la dernière version
3. **Testez** avec une configuration propre

### Template de signalement de bug

```markdown
## Description du bug
Description claire et concise du problème.

## Étapes pour reproduire
1. Aller à '...'
2. Cliquer sur '....'
3. Voir l'erreur

## Comportement attendu
Description de ce qui devrait se passer.

## Comportement actuel
Description de ce qui se passe réellement.

## Environnement
- OS: [e.g. Windows 10, macOS Big Sur, Ubuntu 20.04]
- Browser: [e.g. Chrome 95, Firefox 94]
- Version PHP: [e.g. 8.2]
- Version Symfony: [e.g. 7.3]

## Captures d'écran
Si applicable, ajoutez des captures d'écran.

## Logs d'erreur
```
Collez ici les logs d'erreur pertinents
```

## Informations additionnelles
Toute autre information utile.
```

## 💡 Proposer une fonctionnalité

### Template de proposition

```markdown
## Résumé de la fonctionnalité
Brève description de la fonctionnalité proposée.

## Motivation
Pourquoi cette fonctionnalité serait-elle utile ?

## Description détaillée
Description complète du comportement souhaité.

## Alternatives considérées
Autres approches que vous avez envisagées.

## Implémentation possible
Si vous avez des idées sur l'implémentation.
```

## 🧪 Tests

### Structure des tests

```
tests/
├── Unit/                   # Tests unitaires
│   ├── Controller/
│   ├── Entity/
│   └── Repository/
├── Integration/           # Tests d'intégration
└── Functional/           # Tests fonctionnels (end-to-end)
```

### Écrire des tests

#### Test unitaire exemple

```php
<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskCreation(): void
    {
        $user = new User();
        $task = new Task();
        $task->setTitle('Test Task')
            ->setContent('Test Content')
            ->setUser($user);

        $this->assertEquals('Test Task', $task->getTitle());
        $this->assertEquals('Test Content', $task->getContent());
        $this->assertEquals($user, $task->getUser());
        $this->assertFalse($task->isDone());
    }
}
```

#### Test fonctionnel exemple

```php
<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testTaskListPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }
}
```

## 📋 Checklist de révision de code

### Pour l'auteur

- [ ] Le code suit les standards du projet
- [ ] Les tests passent
- [ ] La documentation est mise à jour
- [ ] Les commentaires expliquent le "pourquoi", pas le "quoi"
- [ ] Pas de code commenté/mort
- [ ] Pas de console.log ou var_dump oubliés
- [ ] Les variables ont des noms explicites
- [ ] Les fonctions sont courtes et font une seule chose

### Pour le réviseur

- [ ] La fonctionnalité fonctionne comme attendu
- [ ] Le code est lisible et maintenable
- [ ] Les tests couvrent les cas d'usage importants
- [ ] Pas de régression introduite
- [ ] Les bonnes pratiques Symfony sont respectées
- [ ] La sécurité est prise en compte

## 🔒 Sécurité

Si vous découvrez une vulnérabilité de sécurité, **ne la publiez pas dans les issues publiques**.

Contactez l'équipe de maintenance directement :
- Email : [votre-email-de-securite@example.com]
- Ou via les messages privés GitHub

## 📞 Support

### Où obtenir de l'aide

- **Documentation** : Consultez le [README.md](README.md) et ce guide
- **Issues GitHub** : [Issues du projet](https://github.com/votre-username/TodoList/issues)
- **Discussions** : [Discussions GitHub](https://github.com/votre-username/TodoList/discussions)

### Ressources utiles

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Documentation Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.15/index.html)
- [PHPUnit Documentation](https://phpunit.readthedocs.io/)
- [Docker Documentation](https://docs.docker.com/)

## 🎯 Roadmap du projet

### Fonctionnalités prévues

- [ ] API REST pour les tâches
- [ ] Notifications par email
- [ ] Système de catégories avancé
- [ ] Interface mobile responsive
- [ ] Export des tâches (PDF, CSV)

### Améliorations techniques

- [ ] Migration vers Symfony 8.0
- [ ] Optimisation des performances
- [ ] Amélioration de la couverture de tests
- [ ] Documentation API avec OpenAPI

## 🏆 Contributeurs

Merci à tous les contributeurs qui ont participé à ce projet !

<!-- Cette section sera automatiquement mise à jour -->

## 📄 Licence

En contribuant à ce projet, vous acceptez que vos contributions soient sous la même licence que le projet (MIT).

---

**Questions ?** N'hésitez pas à ouvrir une [discussion](https://github.com/votre-username/TodoList/discussions) !

Merci de contribuer au projet TodoList ! 🚀
