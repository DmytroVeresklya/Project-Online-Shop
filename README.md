# Project-Online-Shop

## Table of contents
* [General info](#general-info)
* [Technologies](#technologies)
* [Setup](#setup)

## General info
This project is a simple online store that allows users to shop online and receive product consultations through chat.
It features a chat function that enables customers to communicate with the store's administrator,
where they can receive answers to their questions and get assistance with placing an order.

For the convenience of users, the project also has the
ability to place orders for both registered and non-registered users.

## Technologies
Project is created with:
* PHP: 8.1
* PostgreSQL: 15.1
* Symfony: 6.2
* Twig: 3.0
* imagine: 1.3
* lexik jwt-authentication: 2.16

## Setup
To run this project using Docker, please follow the steps below:

1. Clone the project repository:
```
git clone https://github.com/DmytroVeresklya/Project-Online-Shop.git
```
2. Navigate to the project directory:
```
cd project-directory
```
3. Create .env file with fields:
```
DATABASE_NAME=name
DATABASE_HOST=localhost
DATABASE_PORT=5432
DATABASE_USER=postgres
DATABASE_PASSWORD=password
DATABASE_URL=postgresql://postgres:password@127.0.0.1:5432/name

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=text

PROJECT_NAME=PASTE_YOUR_PROJECT_DIR_NAME!

PUID=1000
PGID=1000

NGINX_HOST_HTTP_PORT=888
INSTALL_XDEBUG=true
```
4. Change <b>PASTE_YOUR_PROJECT_DIR_NAME!</b> in <b>.env</b> and <b>docker/nginx/default</b> to your project dir name! 


5. Build the Docker images:
```
docker-compose -f docker/docker-compose.yml build
```
6. Run the Docker images:
```
docker-compose -f docker/docker-compose.yml up
```
7. Up database:
```
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load --purge-with-truncate --no-interaction
```
8. Access the application at http://localhost:80 in your web browser.
