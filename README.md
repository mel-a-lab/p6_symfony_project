# Symfony project : community site creation of Snowtricks
[![SymfonyInsight](https://insight.symfony.com/projects/0bf4eb84-449a-4c34-9aef-220a426c3f66/big.svg)](https://insight.symfony.com/projects/0bf4eb84-449a-4c34-9aef-220a426c3f66)


## Description
A snowboard tricks directory

## How to use this repository

- create a local database named "p6_symfony_project"
- clone the repository in your www folder if you have wamp. If you don't have wamp, clone the repository to the projects folder of your local server environment.
```text
git clone https://github.com/mel-a-lab/p6_symfony_project
```
1. Prerequisites: Before installing Symfony, make sure you have the following requirements installed on your system:

PHP >= 8.0
Composer

2. Create a new Symfony project: Open your terminal and navigate to the directory where you want to create your Symfony project. Then run the following command:

- 
- edit in the .env.local file located and modify the login credentials to the database
- finally, open your browser and go to localhost/p6_symfony_project

```bash
composer create-project symfony/website-skeleton my_project_name
```

Replace my_project_name with the desired name for your project.

3. Configure the database: Symfony requires a database to work. Open the .env file in the root of your project and configure the DATABASE_URL parameter with your database connection details. For example:

```bash
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

4. Install dependencies: Navigate to your project's root directory and run the following command to install the required dependencies:

```bash
composer install
```

5. Run the Symfony development server: Start the built-in development server by running the following command:

```bash
symfony server:start
```

6. Access your Symfony application: Open your web browser and visit http://localhost:8000 to access your Symfony application.





