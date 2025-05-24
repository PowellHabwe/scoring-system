Judge Scoring System
A web application built on the LAMP stack for managing judges, scoring participants, and displaying a live scoreboard for an event.
Overview
This project fulfills the requirements for a scoring system with an admin panel to manage judges and participants, a judge portal for scoring, and a public scoreboard that auto-refreshes every 30 seconds. It uses PHP 8.1, MySQL, Apache, and HTML/CSS/JavaScript.
Setup Instructions
The project can be run locally on Ubuntu 24.04 or hosted online (e.g., InfinityFree). The source code is hosted on GitHub, and a live demo can be deployed for public access.
Local Setup (Ubuntu 24.04)
Prerequisites

Operating System: Ubuntu 24.04
Software:
Apache2
MySQL 8.0 or later
PHP 8.1 with extensions (php8.1, php8.1-mysql, php8.1-pdo, php8.1-common, php8.1-cli, libapache2-mod-php8.1)
Git


A modern web browser

Installation Steps

Install Dependencies:
sudo apt update
sudo apt install apache2 mysql-server php8.1 php8.1-mysql php8.1-pdo php8.1-common php8.1-cli libapache2-mod-php8.1 git


Clone the Repository:
git clone https://github.com/PowellHabwe/scoring-system.git /var/www/html/scoring-app
cd /var/www/html/scoring-app


Set Up the Database:

Log in to MySQL:mysql -u root -p


Create the database:CREATE DATABASE scoring_app;
EXIT;


Import the schema:mysql -u root -p scoring_app < schema.sql


Import sample data:mysql -u root -p scoring_app < sample_data.sql




Configure Database Connection:

Copy db.sample.php to db.php:cp db.sample.php db.php


Edit db.php with your MySQL credentials:$host = 'localhost';
$db   = 'scoring_app';
$user = 'root';
$pass = 'your_mysql_password'; // Replace with your password
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (\PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}




Set Permissions:
sudo chown -R $USER:www-data /var/www/html/scoring-app
sudo chmod -R 775 /var/www/html/scoring-app


Start Services:
sudo systemctl start apache2 mysql
sudo systemctl enable apache2 mysql


Access the Application:

Open http://localhost/scoring-app in a browser.



