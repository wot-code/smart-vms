# 🛡️ Smart Visitor Management System (VMS)

A professional, offline-resilient Visitor Management System built with Laravel, designed for high-security institutions. This project features a PWA-compliant registration interface, digital signature capture, and automated security audit logging.

---

## 🚀 Technical Requirements
Before your friend starts, ensure they have the following installed:
*   **XAMPP** (with PHP 8.2 or higher)
*   **Composer** (PHP dependency manager)
*   **Git** (optional, for cloning)

> [!NOTE]
> **Built Assets Included:** I have updated the project settings so that the compiled UI (CSS/JS) is now included in the repository. Your friend no longer needs to install Node.js or run build commands to see the styled UI.

---

## 🛠️ Installation Guide (Step-by-Step)

### 1. Clone or Extract the Project
Open your terminal (or CMD) and navigate to your `xampp/htdocs` folder:
```bash
cd C:\xampp\htdocs
git clone https://github.com/wot-code/smart-vms.git
cd smart-vms
```

### 2. Install Dependencies
Run this command to install all the Laravel and Livewire packages:
```bash
composer install
```

### 3. Configure the Environment
Copy the example environment file:
```bash
cp .env.example .env
```
Open the new `.env` file and look for these lines. Update them to match your local database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup the Database
1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named **`vms_db`**.
3.  Click on the **"Import"** tab.
4.  Choose the file named **`vms_db_dump.sql`** (located in the project root folder) and click **Go**.

### 5. Generate Security Key
```bash
php artisan key:generate
```

### 6. Run the Project
Start the Laravel development server:
```bash
php artisan serve
```
The project is now live at: **`http://127.0.0.1:8000`**

---

## 📱 Features to Demonstrate
*   **Visitor Check-in:** `http://127.0.0.1:8000/checkin` (Support offline mode & signatures).
*   **Admin Dashboard:** Log in to manage visitors and view the security audit trail.
*   **Role Access:** Admin, Host, and Guard specific views.

---

## 🔑 Default Credentials
(Check the `users` table in your database for existing accounts or create a new one using the provided seeders).

---

## 📜 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

