# 🚀 Bitcoin Transaction Viewer - Quick Start Guide

## ⚡ Instant Setup (3 Steps)

### Step 1: Configure Database (NEW!)
Edit `config.php` and update your database credentials as needed.

### Step 2: Start the Server
```bash
# Double-click this file (Windows):
start-server.bat

# OR run manually:
php -S localhost:8000
```

### Step 3: Launch the App
```bash
# Open in browser:
http://localhost:8000/project-with-database.html
```

## 🎯 How to Use

### Search Examples
Try searching for these types of data:

1. **Transaction ID**: `abc123def456...` (full or partial)
2. **Block Hash**: `000000000019d6689c085ae165831e93...`
3. **Bitcoin Address**: `1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa`

### Visual Indicators
- **🔍 Pink Star**: Your searched transaction (impossible to miss!)
- **Blue Boxes**: Regular transaction nodes
- **Info Popups**: Appear when you zoom in
- **Connections**: Lines show transaction relationships

### Controls
- **Search**: Enter any transaction data and hit Enter
- **Zoom**: Mouse wheel or pinch
- **Pan**: Click and drag
- **Node Click**: Shows options (List details / Show connections only)
- **Double Click**: Copies transaction ID to clipboard

## 🔧 Troubleshooting

### Quick Fixes
```bash
# Database issues?
http://localhost:8000/test-db.php

# API problems?
http://localhost:8000/debug-database.html

# Get sample data:
http://localhost:8000/get-txids.php
http://localhost:8000/get-blocks.php
```

### Common Issues

**"Node not found in network"**
- Transaction exists but has no connections to other transactions
- Try searching for a different transaction ID

**"Database connection failed"**
- Start PostgreSQL service
- Check if 'bitcoin' database exists
- Verify credentials in `config.php`

**Search highlighting not working**
- Check browser console (F12)
- Look for large pink star with "🔍 SEARCHED:" prefix
- Try zooming out to see full graph

## 📊 What You Need

### Database Requirements
- **PostgreSQL** running
- **Database**: `bitcoin`
- **Table**: `tx_summary`
- **Credentials**: Set in `config.php`

### Table Structure
```sql
CREATE TABLE tx_summary (
    txid TEXT,
    block_hash TEXT,
    timestamp BIGINT,
    merkle_root TEXT,
    input_addresses TEXT,
    prev_txids TEXT,
    output_addresses TEXT,
    total_input NUMERIC,
    total_output NUMERIC,
    fees NUMERIC
);
```

## 🎉 Success Indicators

When everything works correctly, you should see:
1. ✅ Graph loads with blue transaction nodes
2. ✅ Search finds transactions and highlights them as pink stars
3. ✅ Clicking nodes shows transaction details
4. ✅ Zoom reveals additional information
5. ✅ No errors in browser console

## 📞 Need Help?

1. **Setup Issues**: Run `setup-check.php` first
2. **Database Problems**: Check `test-db.php`
3. **API Issues**: Use `debug-database.html`
4. **Search Problems**: Check browser console (F12)

---

**Your Bitcoin transaction network is ready to explore!** 🚀

Start by searching for any transaction ID, block hash, or Bitcoin address to see the magic happen!