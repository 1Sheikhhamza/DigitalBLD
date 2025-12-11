# Digital BLD - Launch Instructions

## 1. Quick Start
Double-click the **`start_server.command`** file in this folder.
The site will run at: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 2. Setup (If running for the first time on a new machine)
1.  **Database**:
    *   Create a MySQL database named `bldlegalized_bld`.
    *   Import the file `database/dump.sql`.
2.  **Requirements**:
    *   Ensure PHP and MySQL are installed.

## 3. Configuration
The settings are in `application/config/database.php`.
If your database password changes, update it there.
