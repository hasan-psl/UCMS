# UCMS

A lightweight, dynamic University Club Management System (UCMS) built with PHP, MySQL, and vanilla JS — complete with authentication, dashboards, and real-time modals.

---

# UCMS — University Club Management System

UCMS (University Club Management System) is a web-based platform for managing university clubs, events, and memberships. It enables students to discover clubs, join events, and manage memberships, while providing admins with an efficient way to oversee all operations.

![UCMS Dashboard Example](https://i.imgur.com/D5glZDk.jpeg)

## Features

* **User Authentication**: Secure login for admin users.
* **Club Directory**: View and manage all university clubs, their details, and advisor contacts.
* **Event Management**: Browse upcoming events organized by clubs.
* **Members Area**: View club members, their positions, and contact details.
* **Admin Dashboard**: Admin panel for CRUD operations on clubs, events, and members.
* **Responsive Design**: Built with modern HTML5, CSS3, and JavaScript for a seamless multi-device experience.

## Stack

* **Frontend**: HTML5, CSS3, JavaScript (`/assets/js/app.js`)
* **Backend**: PHP (see `/backend/php/` for endpoints)
* **Database**: MySQL/MariaDB (schema in `/sql/schema.sql` and `/ucms.sql`)
* **Authentication**: Session-based login for admins

---

## Dependency Setup

Install PHP, MySQL/MariaDB, and required PHP extensions:

```bash
sudo apt install php php-mysql mariadb-server
```

Check if the MariaDB service is active and running:

```bash
sudo systemctl status mariadb
```

You should see something like this:

> ● mariadb.service - MariaDB 11.8.3 database server
> 
> Loaded: loaded (/usr/lib/systemd/system/mariadb.service; enabled; preset: enabled)
> 
> Active: active (running) since Thu 2025-11-06 13:33:34 +06; 1h 2min ago

If it says **inactive**, start it:

```bash
sudo systemctl start mariadb
```

---

## Database Setup

### 1. Clone this repository

```bash
git clone https://github.com/yourusername/UCMS.git
cd UCMS
```


### 2. Open MariaDB shell

```bash
sudo mariadb
```

### 3. Set root password and TCP access

Inside the MariaDB shell:

```sql
ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
```

### 4. Create and import UCMS database

```bash
sudo mariadb
```

Inside MariaDB shell:

```sql
CREATE DATABASE ucms;
USE ucms;
SOURCE FULL_FILE_PATH/ucms.sql;
EXIT;
```

### 5. Test your connection

```bash
mysql -h 127.0.0.1 -u root -p ucms
```

> Press **Enter** when prompted for a password.
> If you see `MariaDB [ucms]>` — means the database setup and connection is successfull!
> Exit the shell with:

```sql
EXIT;
```

---

## Quick Start

### 1. Configure Database

* Default admin credentials are in the seed section of `schema.sql` or `ucms.sql`. Update them before first login.

### 2. Run the Project

Start a PHP development server inside the project/repository root folder (eg. /home/user/Downloads/UCMS/):

```bash
php -S 127.0.0.1:8000
```

Open your browser and visit: `http://127.0.0.1:8000`

To stop the server:

```bash
Ctrl+Shift+C
```

---

## File Structure

```
UCMS/
├── assets/
│   ├── css/style.css
│   ├── images/
│       ├── bg.jpg
│       └── bg_crop.jpg
│   └── js/app.js
├── backend/
│   └── php/
│       ├── auth.php
│       ├── clubs.php
│       ├── events.php
│       └── members.php
├── sql/
│   └── schema.sql
├── ucms.sql
├── *.html (index, login, clubs, events, members, dashboard)
└── credits.md
```

---

## Screenshots

* ![Home Page](https://i.imgur.com/MzCyAtk.jpeg)
* ![Clubs](https://i.imgur.com/meqmBtt.jpeg)
* ![Events](https://i.imgur.com/cQgrpM6.jpeg)
* ![Members](https://i.imgur.com/EgrnvvI.png)
* ![Dashboard](https://i.imgur.com/D5glZDk.jpeg)
* ![Login Page](https://i.imgur.com/cDOjJV2.png)
* ![Pop-up/Modal 1](https://i.imgur.com/JJK4JOk.png)
* ![Pop-up/Modal 2](https://i.imgur.com/Jr2ppyy.png)

---

## Credits

* Main background photo by [Colin Lloyd](https://unsplash.com/@onthesearchforpineapples) on [Unsplash](https://unsplash.com/photos/people-watching-fireworks-display-during-nighttime-AlO0J3WN3Tw).

---

## License

MIT License

---
