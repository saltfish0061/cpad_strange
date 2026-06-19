This directory contains the database structure, seeding scripts, and backups.

Files:
- schema.sql: Database table creation statements (defines the database structure).
- seeders.sql: Sample/seed data inserts (adds initial mock users, menus, and orders).
- cpad_03_Strange.sql: Complete MariaDB dump backup file (containing both schema and data).

Instructions for Team Members:
- If you make any updates to the database structure (e.g. creating/modifying tables, adding fields, changing columns), please update `schema.sql`.
- If you add new seed or testing data (e.g. additional menus, users, test orders), please update `seeders.sql`.
- Try to update these files directly so team members can import them easily when updating their local environments.
- **Before final submission or final update**, please regenerate the complete database backup file (`cpad_03_Strange.sql`) using:
  ```bash
  mysqldump --user=cpad --password cpad_03_strange > cpad_03_Strange.sql
  ```
