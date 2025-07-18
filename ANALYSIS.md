# Audit de testabilité

## Classification des fichiers

| Fichier/Classe | Type de test recommandé | Outils utilisables | Justification                | Priorité (1-3) |
|----------------|------------------------|-------------------|---------------          |----------------|
|Entity/Task.php | Unitaire               | TestCase          |fct simple:getteur,setteur   |                 1 |
|Entity/User.php | Unitaire               | TestCase          |getteur ,setteur |                             1 |
|Service/TaskService.php|Unitaire avec Mock|TestCase|mocker Repository pour tester createTask, deleteTask ..| 1 |
|Security/LoginFormAuthenticator.php|Fonctionnel|WebTestCase  |Depend des firewall et session               | 3 |
|Security/Voter/TaskVoter.php| Unitaire   | TestCase          |logique clair et isolée                      | 2 |
|Repository/TaskRepository.php|Integration| KernelTestCase    |Necessite Doctrine                           | 2 |
|Repository/UserRepository.php|Integration| KernelTestCase    |Necessite Doctrine                           | 2 |
|Form/RegistrationFormType.php|Fonctionnel|WebTestCase        |Simuler un formulaire                        | 3 |
|Form/TaskType.php            |Fonctionnel|WebTestCase        |Simuler un formulaire                        | 3 |
|Controller/HomeController.php|Fonctionnel|WebTestCase        |Utilise route et vue                         | 3 |
|Controller/RegistratioController.php|Fonctionnel|WebTestCase |Utilise formulaire hashage                   | 3 |
|Controller/SecurityController.php|Fonctionnel|WebTestCase    |Dépend du firewall et de la session          | 3 |
|Controller/TaskController.php|Fonctionnel|WebTestCase|Gère tout le CRUD, la sécurité, la BDD  et les formulaires|1|


## Stratégie de test adoptée

### Priorité 1 (Tests immédiats)
- Justifiez pourquoi ces fichiers sont prioritaires:

Les tests de priorité 1 ciblent les composants critiques et testables facilement en isolation.
Ces classes sont centrales dans le fonctionnement de l'application (gestion des tâches).
Les entités possèdent des méthodes métier (addTask, removeTask) qui doivent être testées rapidement.
Le service TaskService manipule directement la base via Doctrine et peut être mocké.
Le contrôleur TaskController regroupe toutes les fonctionnalités (CRUD), donc son bon fonctionnement est essentiel.

- Expliquez leur impact sur l'application:

Toute anomalie dans ces classes affecterait directement la gestion des tâches, l'enregistrement en BDD et l'affichage utilisateur.
Ces tests assurent la stabilité du cœur fonctionnel.

### Priorité 2 (Tests importants)
- Fichiers avec dépendances modérées
- Nécessitent des mocks/stubs

Les fichiers de priorité 2 ont une importance élevée mais nécessitent un contexte ou des dépendances plus complexes

Ces composants sont importants pour les interactions avec Doctrine ou la sécurité.

Ils peuvent être testés avec KernelTestCase , mais nécessitent des jeux de données.

TaskVoter.php a une logique métier simple, mais intégrée dans le système de sécurité.

Ils interviennent dans des aspects clés : création utilisateur, persistance des données, autorisation.

Des bugs ici peuvent compromettre l’accès ou la fiabilité des données.

### Priorité 3 (Tests complexes)
- Tests d'intégration
- Workflows complets

Ces fichiers sont testables, mais impliquent un contexte d’exécution Symfony complet, avec formulaire, routage, sessions, validation, etc.

Ils sont dépendants des composants Symfony (Form, Validator, Security, Routing, etc.).

Ils nécessitent un test d’intégration global (souvent long à mettre en place).

Leurs erreurs sont plus visibles via tests fonctionnels complets.

S’ils échouent, cela impacte l’expérience utilisateur (accès, navigation, validation).

Moins critiques que les services métier, mais importants pour la robustesse.

## Difficultés identifiées
- Listez les défis techniques rencontrés
- Proposez des solutions
Défi technique	                --------|Solution proposée
----------------------------------------|------------------------------------
Absence de tests initiaux		        |Initialiser PHPUnit (composer require --dev phpunit/phpunit)

Accès BDD requis pour certains tests	|utiliser une BDD dédiée aux tests

Manque de fixtures de données		    |Utiliser DoctrineFixturesBundle ou instancier manuellement les entités

