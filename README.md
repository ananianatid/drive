# Project Overview

This project is a web application built with Laravel and FilamentPHP. It provides a comprehensive administration panel for managing various aspects of an educational or driving school system.

## Project Structure

The core application logic resides in the `app/` directory, organized as follows:

-   `app/Filament/Resources`: Contains the FilamentPHP resources that define the administrative interface for different data models. Each resource (e.g., `AcademicClassResource`, `StudentResource`, `TeacherResource`) provides CRUD (Create, Read, Update, Delete) operations and defines how data is presented and managed in the admin panel.
-   `app/Models`: Defines the Eloquent ORM models, representing the database tables and their relationships. These models encapsulate the business logic and data structure of the application (e.g., `AcademicClass`, `Student`, `Teacher`, `Course`, `Exam`, `License`, `User`, `Role`, `Permission`).
-   `app/Http/Controllers`: Contains the application's controllers, though for a Filament-heavy application, much of the interaction logic might be handled directly by Filament resources.
-   `app/Providers`: Includes service providers that register services and bootstrap the application, notably `app/Providers/Filament/AdminPanelProvider.php`, which configures the Filament admin panel.

## Database

The database schema is defined and managed through Laravel migrations, located in the `database/migrations/` directory. Key tables include:

-   `users`: Stores user information, including roles and permissions.
-   `academic_classes`: Manages academic class details.
-   `students`: Contains student records.
-   `teachers`: Stores teacher information.
-   `courses`: Defines courses offered.
-   `exams`: Manages exam details and results.
-   `presences`: Tracks student and teacher presences.
-   `results`: Stores academic results.
-   `identity_cards`: Manages identity card information.
-   `licenses`: Handles driving license details.
-   `periods`: Defines academic or lesson periods.
-   `vehicules`: Manages vehicle information for driving schools.
-   `roles`: Defines user roles (e.g., Administrator, Student, Teacher).
-   `permissions`: Manages system permissions.
-   `administrators`: Stores administrator specific information.
-   `role_users`, `permission_roles`, `permission_users`: Pivot tables for managing many-to-many relationships between users, roles, and permissions.

## FilamentPHP Admin Panel

FilamentPHP is used to rapidly build the administration interface. The `AdminPanelProvider` configures the panel, including:

-   **Authentication:** Handles user login.
-   **Resources:** Automatically discovers and registers resources from `app/Filament/Resources`, providing a user-friendly interface for managing all defined models.
-   **Pages:** Includes a dashboard page for an overview.
-   **Widgets:** Provides various widgets on the dashboard for quick insights.

## Installation

To set up the project locally:

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/ananianatid/drive.git
    cd drive
    ```
2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
3.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```
4.  **Create a copy of your `.env.example` file as `.env`:**
    ```bash
    cp .env.example .env
    ```
5.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```
6.  **Configure your database in the `.env` file.**
7.  **Run database migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```
8.  **Link storage:**
    ```bash
    php artisan storage:link
    ```
9.  **Run the development server:**
    ```bash
    php artisan serve
    npm run dev
    ```

Access the admin panel by navigating to `/admin` in your browser. You can log in with the default administrator user created by the seeder (if available), or create a new user via the `UserResource` in the admin panel.
