🍽️ Restaurant Management System
A Laravel-based Restaurant Management System that provides functionality to manage orders, users, and roles with permissions. This system includes:
    • Role & permission-based access
    • Super Admin access for user creation and management
    • Order tracking with status filters (Pending, In Progress, Completed)
    • Dynamic dashboard with DataTables integration

🚀 Getting Started
Follow these steps to clone and run the project locally:
1. Clone the Repository
git clone https://github.com/akila199910/LayoutIndex.git
cd LayoutIndex
2. Install PHP Dependencies
Make sure you have PHP 8.0+ and Composer installed.
composer install
3. Copy and Configure Environment File
cp .env.example .env
Update the .env file with your local database configuration:
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
4. Create a Database
Create a new empty database in your local MySQL server before running migrations.

⚙️ Migrate and Seed the Database
php artisan migrate --seed
This will:
    • Create all necessary tables
    • Seed default permissions
    • Create a Super Admin account

🔐 Default Super Admin Login
Email:    admin@gmail.com
Password: 12345678
Only the Super Admin can create new users and assign permissions.

👤 User Management
    • When creating a new user, you can assign specific permissions.
    • Default password for newly created users: User@1234
    • Permissions include access to Orders, Concessions, and Users (Read, Create, Update, Delete, etc.)

🧶 Run the App
Start the Laravel development server:
php artisan serve
Visit http://localhost:8000

📁 Tech Stack
    • Laravel 8.75+
    • Bootstrap (Admin UI)
    • jQuery & DataTables
    • MySQL
    • Font Awesome

🤝 Contributing
Feel free to fork the project and submit a PR!

