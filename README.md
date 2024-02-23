# ToDo & Co

TodoList is an MVP application developed with symfony 3.1 and PHP 5.5.9. The startup
whose core business is an application to manage its daily tasks.
The company has succeeded in raising funds and now wants to improve the quality of the application
and reduce its technical debt.

## Objectives:

- Write unit and functional tests :white_check_mark:
- Reduce the technical debt of the application :white_check_mark:
- Fix bugs :white_check_mark:
- Add features :white_check_mark:
- Perform a quality and performance audit :white_check_mark:

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c00dbb2ceec1457d84f4f13cd10bd8d7)](https://app.codacy.com/gh/MarionDoubeck/OCphp_P8_ToDoAndCo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Configuration 

- php (CLI) 8.3.2
- mysql 8.2.0
- CLI de symfony 5.8.6,
- composer 2.7.1

## Projet Installation

### Cloner the Projet

To obtain a local copy of the project, use the following command:

```
git clone https://github.com/MarionDoubeck/OCphp_P8_ToDoAndCo
```
### Install Dependencies

In your terminal, run the following command to install the project dependencies using Composer:

```
composer install
```
### Environment Configuration
Ensure that your environment is properly configured, including the database. You'll need to create a env.local file for your local configuration. Here's an example of the content for this file:

```
DATABASE_URL=mysql://nom_utilisateur:mot_de_passe@localhost:3306/nom_de_la_base_de_donnees
APP_DEBUG=true
APP_SECRET=cle_secrete_unique_pour_votre_application
APP_URL=http://localhost:8000
```
Make sure to customize the values with your specific information.

### Migrations
To create the database tables, execute the migration using the following command:

```
php bin/console doctrine:migrations:migrate
```

### Loading Fixtures
To load development data into the database, execute the following command:
```
php bin/console doctrine:fixtures:load --env=dev  --group=groupApp
```

To load test data into the database, execute the following command:
```
php bin/console doctrine:fixtures:load --env=test  --group=groupTest
```

### Running the Application
To run the application, execute the following command:

```
symfony serve -d
```

### Running Tests
To run the test coverage, execute the following command:
```
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html=coverage
```


