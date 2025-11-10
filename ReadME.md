# PHP Blog Application
This is a simple PHP blog project built with PHP, MySQL (MariaDB), and a minimal frontend.

## Setup Instructions

### 1. Install PHP & Extensions (Linux - Arch)
```bash
sudo pacman -S php php-mysqli php-curl php-gd php-mbstring

### 2. Install and start mariadb
sudo pacman -S mariadb
sudo systemctl start mariadb

### 3. Import the database
mysql -u root -p < schema.sql

### 4. Run the PHP Project
cd /path/to/your/blog-app
php -S localhost:8000

## after logging in and creating the first account (which you want to keep as admin):
USE blog_db;
UPDATE users SET is_admin = 1 WHERE username = 'your_admin_username';








1. For running the project on Linux (Arch)

    install the basics of php
    sudo pacman -S php php-mysqli php-curl php-gd php-mbstring

    sudo pacman -S mariadb
    sudo systemctl start mariadb

2. Import the database:
   mysql -u root -p < schema.sql

-- In your VSCode
2. cd /path/to/your/blog-app
   php -S localhost:8000


3. process is mostly same for windows
   On Windows, you’d typically use XAMPP/WAMP instead of installing PHP and MariaDB manually.
   Make sure your PHP code’s database connection matches the user, password, and database name you created.