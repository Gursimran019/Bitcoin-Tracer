# Bitcoin Transaction Viewer

A web-based application for visualizing Bitcoin transaction networks with interactive graph visualization.

## 🚀 Quick Start

### Prerequisites
- **XAMPP** or **PHP 7.4+** installed
- **PostgreSQL** database running
- **Bitcoin database** with `tx_summary` table

### 1. Database Configuration (NEW!)
Edit `config.php` to set your database credentials.
**Benefits**: Only one file to edit when moving to new devices!

### 2. Start the Application
```bash
# Option 1: Use the batch file (Windows)
start-server.bat

# Option 2: Manual start
php -S localhost:8000
