# Project: Online Food & Drink Ordering System

[GitHub Repo](https://github.com/saltfish0061/cpad_strange.git)

## Overview
This is a web-based Online Food & Drink Ordering System designed to centralize and streamline the ordering process for cafeteria vendors, students, and staff. The system addresses issues such as long waiting times, lack of centralized management, and order inaccuracies.

## Features
- Vendor Management
- Student & Staff Ordering Portal
- Real-time Order Tracking
- centralized Database Management

## Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript (Vue.js 3 via CDN, Lottie Player)
- **Backend**: PHP (Slim 4 Framework)
- **Database**: MariaDB / MySQL

---

## Code Directory Structure
The repository is structured as follows:
- **Root Directory (`cpad-project/`)**: Contains the `README.md` documentation file.
- **Application Root (`strange-3/`)**: Contains the vendor libraries, Composer config files, and the main web directory.
- **Main Code Location (`strange-3/public/src/`)**: This is where all the application source code is located:
  - `src/customer/`: Contains customer-facing pages (menu, cart, orders, profile).
  - `src/vendor/`: Contains vendor/admin dashboard and merchant controls.
  - `src/auth/`: Contains the login page and session authentication code.

---

## Setup Instructions

### 1. Project Initialization
1.  Clone the repository into your XAMPP `htdocs` folder (e.g., `C:\xampp\htdocs\cpad-project`).
2.  Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 2. Install Dependencies
Since this project uses the PHP Slim 4 Framework for API routing:
1. Make sure you have **Composer** installed.
2. Open your terminal in the `strange-3` subdirectory and run:
   ```bash
   cd strange-3
   composer install
   ```

### 3. Database Setup (Choose ONE method below)

#### Method A: Using phpMyAdmin (SQL Tab)
1.  Open `http://localhost/phpmyadmin/`.
2.  Click on the **SQL** tab at the top.
3.  Paste the following commands to initialize the database and user:
     ```sql
     CREATE DATABASE IF NOT EXISTS cpad_03_strange;
     USE cpad_03_strange;
     CREATE USER IF NOT EXISTS 'cpad'@'localhost' IDENTIFIED BY 'cpadPassword';
     GRANT ALL PRIVILEGES ON cpad_03_strange.* TO 'cpad'@'localhost';
     FLUSH PRIVILEGES;
     ```
4.  Open the SQL dump file located at `strange-3/public/dbdocs/cpad_03_Strange.sql`, copy all content, and paste it **after** the code above.
5.  Click **Go**.

#### Method B: Using MySQL Command Line
1.  Open your terminal or XAMPP Shell.
2.  Log in to MySQL 
3.  Run the following commands:
     ```sql
     CREATE DATABASE IF NOT EXISTS cpad_03_strange;
     USE cpad_03_strange;
     CREATE USER IF NOT EXISTS 'cpad'@'localhost' IDENTIFIED BY 'cpadPassword';
     GRANT ALL PRIVILEGES ON cpad_03_strange.* TO 'cpad'@'localhost';
     FLUSH PRIVILEGES;
     SOURCE C:/xampp/htdocs/cpad-project/strange-3/public/dbdocs/cpad_03_Strange.sql;
     ```

### 4. Accessing the Application
Once the database is set up and Apache/MySQL are running, you can access the application:
1. Open your web browser.
2. Navigate directly to your group's folder at: `http://localhost/cpad-project/strange-3/`
3. The directory's `index.php` will automatically redirect you to the main home page at: `http://localhost/cpad-project/strange-3/public/index.php`


## Database Backup
To back up the database for submission, run from the command line:

```bash
mariadb-dump --user=cpad --password cpad_03_strange > strange-3/public/dbdocs/cpad_03_Strange.sql
```
or
```bash
mysqldump --user=cpad --password cpad_03_strange > strange-3/public/dbdocs/cpad_03_Strange.sql
```
