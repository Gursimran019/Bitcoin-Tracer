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
```

### 3. Open in Browser
Navigate to: `http://localhost:8000/project-with-database.html`

## 📁 Project Structure

```
bitcoin-project/
├── project-with-database.html    # Main application
├── transaction-list.html         # Transaction details page
├── api.php                       # Backend API (uses config.php)
├── config.php                    # Database configuration (NEW!)
├── ncfl-logo.svg                 # NCFL logo
├── About.png                     # About popup image
├── start-server.bat             # Windows startup script
├── SETUP.md                     # Setup instructions (NEW!)
├── QUICK_START.md               # Quick start guide
└── README.md                    # This file
```

## 🔧 Testing & Debugging

### 1. Test Database Connection
Visit: `http://localhost:8000/test-db.php`

### 2. Debug Tools
Visit: `http://localhost:8000/debug-database.html`

### 3. Get Sample Data
- Block hashes: `http://localhost:8000/get-blocks.php`
- Transaction IDs: `http://localhost:8000/get-txids.php`

## 🎯 How to Use

### Search for Transactions
1. **By Transaction ID**: Enter full or partial transaction ID
2. **By Block Hash**: Enter block hash to see all transactions in that block
3. **By Address**: Enter Bitcoin address to find related transactions

### Graph Features
- **Zoom**: Mouse wheel or pinch to zoom
- **Pan**: Click and drag to move around
- **Node Click**: Click any node to see options (List details or Show only connections)
- **Double Click**: Copy transaction ID to clipboard
- **Search Highlighting**: Searched nodes appear as large pink stars with "🔍 SEARCHED:" prefix

### Visual Elements
- **Blue boxes**: Regular transaction nodes
- **Pink star**: Your searched transaction (highly visible)
- **Info popups**: Appear when zoomed in (scale > 1.2)
- **Edge connections**: Show relationships between transactions

## 🐛 Troubleshooting

### Database Issues
```bash
# Common fixes:
1. Start PostgreSQL service
2. Check database name: 'bitcoin'
3. Update credentials in config.php
4. Ensure tx_summary table exists
```

### API Issues
```bash
# Test API directly
http://localhost:8000/api.php

# Debug API calls
http://localhost:8000/debug-database.html
```

### Search Not Working
1. **Check Console**: Open browser F12 → Console tab
2. **Verify Data**: Use debug tools to confirm transaction exists
3. **Try Partial Search**: Use part of transaction ID
4. **Check Network**: Ensure graph is loaded before searching

## 📊 Database Schema

Your `tx_summary` table should have these columns:
```sql
- txid (text) - Transaction ID
- block_hash (text) - Block hash
- timestamp (bigint) - Unix timestamp
- merkle_root (text) - Merkle root
- input_addresses (text) - JSON array or comma-separated
- prev_txids (text) - Previous transaction IDs
- output_addresses (text) - JSON array or comma-separated
- total_input (numeric) - Total input amount
- total_output (numeric) - Total output amount
- fees (numeric) - Transaction fees
```

## 🔍 API Endpoints

- `GET /api.php` - List all transactions
- `GET /api.php?action=chain&txid=<id>` - Get transaction chain
- `GET /api.php/transactions` - Alternative list endpoint
- `GET /api.php/transactions/<txid>` - Alternative chain endpoint

## 🎨 Features

### Graph Visualization
- **Interactive network graph** using vis.js
- **Real-time search highlighting**
- **Zoom-based information display**
- **Drag and drop nodes**
- **Print functionality**

### Search Capabilities
- **Exact transaction ID matching**
- **Partial ID matching**
- **Block hash search**
- **Address-based search**
- **Case-insensitive search**

### User Interface
- **Dark theme** with Bitcoin branding
- **Responsive design**
- **Loading indicators**
- **Error handling**
- **Copy to clipboard**

## 🚨 Common Issues & Solutions

### "Node not found in network"
- The transaction exists in database but not in current graph
- Try searching for a different transaction
- Check if the transaction has connections to other transactions

### "Database connection failed"
- Start PostgreSQL service
- Update database credentials in `config.php`
- Check if `bitcoin` database exists

### "Failed to load transactions"
- Ensure PHP server is running
- Check `api.php` for errors
- Verify table structure matches expected format

### Search highlighting not visible
- Look for large pink star shape with "🔍 SEARCHED:" prefix
- Check browser console for error messages
- Try zooming out to see the full graph

## 📝 Development Notes

### Customization
- Modify `config.php` for different database configurations
- Update CSS in `project-with-database.html` for styling changes
- Adjust graph options in the `drawGraph()` function

### Performance
- Large datasets may require pagination
- Consider limiting search results for better performance
- Graph rendering is optimized for up to ~100 nodes

## 📞 Support

If you encounter issues:
1. Check the browser console (F12)
2. Verify database credentials in `config.php`
3. Ensure PostgreSQL service is running
4. Check your database schema matches requirements

---

**Happy Bitcoin transaction exploring!** 🚀