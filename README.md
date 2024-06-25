# Docker PHP Application with MySQL and PHPMailer

This is a Dockerized PHP application using the Apache server, MySQL database, and PHPMailer for sending emails. The application also includes phpMyAdmin for database management.

## Prerequisites

- Docker
- Docker Compose

## Setup Instructions

### Step 1: Clone the Repository

Clone this repository to your local machine.

```sh
git clone https://github.com/domsius/sonaro.git
cd sonaro
```

### Step 2: Replace the .env placeholders with your Database and SMTP credentials.

```sh
MYSQL_HOST=db
MYSQL_USER=sonaro
MYSQL_PASSWORD=password
MYSQL_DATABASE=sonaro
MYSQL_ROOT_PASSWORD=password

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-email-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME="Poke Notification"
```


### Step 3: Build and start the Docker containers using Docker Compose.

```sh
cp .env ./src/
docker-compose build
docker-compose up -d
docker exec -it php_app composer install
```

### Step 4: Seed users and pokes.

```sh
docker exec -it php_app php /var/www/html/seed_users.php
docker exec -it php_app php /var/www/html/seed_pokes.php
```

### Step 5: visit application

Application -> http://localhost
PHPMyAdmin -> http://localhost:8080