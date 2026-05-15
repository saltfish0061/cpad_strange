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
- **Frontend**: HTML5, CSS3
- **Backend**: PHP
- **Database**: MariaDB / MySQL

---

## Setup Instructions

### 1. Project Initialization
1.  Clone the repository into your XAMPP `htdocs` folder.
2.  Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 2. Database Setup (Choose ONE method below)

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
4.  Open your `cpad_03_strange.sql` file, copy all content, and paste it **after** the code above.
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
    SOURCE c:/path/to/your/cpad_03_strange.sql;
    ```


## Database Backup
To back up the database for submission, use:

mariadb-dump --user=cpad --password cpad_03_strange > cpad_03_strange.sql
or
mysql-dump --user=cpad --password cpad_03_strange > cpad_03_strange.sql


