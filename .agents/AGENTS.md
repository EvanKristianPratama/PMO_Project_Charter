# Database Schema Rules

- **Do NOT use Laravel migrations**: For any database schema changes (creating tables, altering columns, etc.), do not create or run Laravel migration files (`php artisan make:migration` or `php artisan migrate`).
- **Use Manual DDL Queries**: Instead, generate the raw SQL DDL queries (e.g., `CREATE TABLE`, `ALTER TABLE`) and provide them to the user so they can execute them manually in their remote database client (Aiven).
