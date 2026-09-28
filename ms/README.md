# Sales Track Pro — Fixed & Enhanced

## Stack
- PHP 8+
- MySQL / MariaDB
- HTML5, CSS3, vanilla JavaScript
- XAMPP recommended

## XAMPP setup
1. Copy the `ms` folder into:
   `C:\xampp\htdocs\`
2. Start **Apache** and **MySQL** in XAMPP.
3. Open:
   `http://localhost/ms/`
4. The app automatically creates the `in_db` database and required tables on first load.
5. If you want to verify the database connection, open `test.php`.

## Demo accounts
- Owner: `owner1` / `1234`
- Manager: `manager1` / `1234`
- Employee: `emp1` / `1234`
- Legacy accounts are also supported: `owner` / `123`, `manager` / `123`, `employee` / `123`

## Main flows
### Employee
- Dashboard
- Add inventory item
- View/search inventory
- Edit product
- Delete product
- Record sale
- Stock automatically decreases after a successful sale
- Sale is blocked when stock is insufficient

### Manager
- Dashboard
- Employee performance
- Inventory analysis
- Low-stock alerts
- Fast-moving products
- Sales analysis
- Daily revenue and units sold

### Owner
- Dashboard
- Inventory management
- Sales entry
- All manager analytics

## Important fixes
- Replaced the broken/missing dashboard route.
- Fixed malformed logout page.
- Unified the database name to `in_db` to preserve the original project's database.
- Added safe prepared SQL statements to user/product/sales operations.
- Added password hashing while retaining compatibility with old plain-text demo passwords.
- Fixed signup role handling.
- Added missing edit/delete routes.
- Added stock validation and a database transaction for sales.
- Added responsive sidebar/navigation.
- Added search for inventory.
- Added success/error feedback.
- Added consistent animations, hover states, cards, tables, forms, mobile layout and loading-style page transitions.

## Note
The project is intentionally kept as a simple PHP/MySQL application so it remains easy to run and explain in a college project/demo.
