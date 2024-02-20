# Requirements

Before proceeding, ensure that you have Docker Compose and Composer installed on your local computer.

# First Time Installation

After cloning the repository, navigate to the project folder:

```bash
cd kroppa-case
```

## Environment Configuration
Copy Environment File: Locate the .env.example file inside the /src folder and rename it to .env.
If you already have a MySQL setup, please set your variables accordingly. Otherwise, Docker Compose will start a MySQL instance with the following credentials:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=kroppa
DB_USERNAME=kroppa
DB_PASSWORD=secret
```

## Docker Setup
Build the Docker containers:
```
docker-compose build
```
Navigate to the src directory and run the Composer install command:
```cd src
composer install --ignore-platform-req=ext-fileinfo
```
Launch the environment in detached mode:
```
docker-compose up -d
```
Generate the application key using the following command:

```
docker-compose exec php php artisan key:generate
```
Migrate the database using the following command:
```
docker-compose exec php php artisan migrate
```
Once these steps are completed, you should be able to access the Laravel welcome page at  [http://localhost:8088/](http://localhost:8088/) and the environment is ready to use.



The Docker Compose file consists of 4 containers:

- **php**: Our Laravel project
- **mysql**: MySQL for the development environment
- **adminer**: Adminer is included to facilitate viewing MySQL tables during testing. It serves as an alternative to phpMyAdmin. (You can remove it from the Composer file if you do not wish to use it.)
- **nginx**: Serves the files. It shares a volume with the php container and the local machine, enabling development on the local computer without rebuilding the compose files.

To access Adminer, navigate to [http://localhost:8080/](http://localhost:8080/). It should be able to access MySQL because every container in the compose file belongs to a network called 'laravel'.

Try the following credentials:
- **Server**: mysql
- **Username**: kroppa
- **Password**: secret

# Other Requirements

The services are live at the following ports:
- PHP: 9000
- MySQL: 4306
- Adminer: 8080
- NGINX: 8088

Ensure that these ports are available on your local machine for the entire system to function properly.

