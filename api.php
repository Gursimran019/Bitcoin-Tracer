<?php
// Include centralized configuration
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    // Create PDO connection using centralized config
    $pdo = getDatabaseConnection();
    
    // Enhanced routing to handle both query parameters and REST-style URLs
    $action = $_GET['action'] ?? 'list';
    $txid = $_GET['txid'] ?? null;
    
    // Handle REST-style URLs like /api.php/transactions or /api.php/transactions/txid
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $pathInfo = $_SERVER['PATH_INFO'] ?? '';
    
    // Debug logging
    error_log("API called with action: $action, txid: $txid, pathInfo: $pathInfo, requestUri: $requestUri");
    
    // Parse REST-style URLs
    if (strpos($requestUri, '/transactions/') !== false) {
        // Extract transaction ID from URL like /api.php/transactions/abc123
        $parts = explode('/transactions/', $requestUri);
        if (count($parts) > 1) {
            $txid = trim($parts[1]);
            $action = 'chain';
        }
    } elseif (strpos($requestUri, '/transactions') !== false && !$txid) {
        // URL like /api.php/transactions - list all
        $action = 'list';
    }
    
    if ($action === 'chain' && $txid) {
        // Get transaction chain for specific transaction
        getTransactionChain($pdo, $txid);
    } elseif ($action === 'wallet' && isset($_GET['address'])) {
        // Get transactions by wallet address with optional time filtering
        $startTime = $_GET['start_time'] ?? null;
        $endTime = $_GET['end_time'] ?? null;
        getTransactionsByWallet($pdo, $_GET['address'], $startTime, $endTime);
    } else {
        // Get sample transactions for initial load
        getSampleTransactions($pdo);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}

function getSampleTransactions($pdo) {
    try {
        // Get first 50 transactions for initial load
        $stmt = $pdo->prepare("
            SELECT 
                txid,
                block_hash,
                timestamp,
                merkle_root,
                input_addresses,
                prev_txids,
                output_addresses,
                total_input,
                total_output,
                fees
            FROM tx_summary 
            ORDER BY timestamp DESC
            LIMIT 50
        ");
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Transform the data to match frontend format
        $transactions = array_map(function($row) {
            // Parse addresses (assuming they're JSON arrays or comma-separated)
            $input_addresses = parseAddresses($row['input_addresses']);
            $output_addresses = parseAddresses($row['output_addresses']);
            
            return [
                'id' => $row['txid'],
                'from' => $input_addresses,
                'to' => $output_addresses,
                'date' => formatTimestamp($row['timestamp']),
                'amount' => formatAmount($row['total_output']),
                'block_hash' => $row['block_hash'],
                'merkle_root' => $row['merkle_root'],
                'prev_txids' => parseAddresses($row['prev_txids']),
                'total_input' => $row['total_input'],
                'total_output' => $row['total_output'],
                'fees' => $row['fees']
            ];
        }, $results);
        
        echo json_encode($transactions);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch transactions: ' . $e->getMessage()]);
    }
}

function getTransactionChain($pdo, $transaction_id) {
    try {
        // Debug: Log the search
        error_log("Searching for transaction ID: " . $transaction_id);
        
        // First, try to find by transaction ID
        $stmt = $pdo->prepare("
            SELECT 
                txid,
                block_hash,
                timestamp,
                merkle_root,
                input_addresses,
                prev_txids,
                output_addresses,
                total_input,
                total_output,
                fees
            FROM tx_summary 
            WHERE txid = :search_value
        ");
        
        $stmt->bindParam(':search_value', $transaction_id);
        $stmt->execute();
        $mainTx = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If not found by txid, try searching by block_hash
        if (!$mainTx) {
            error_log("Not found by txid, trying block_hash search for: " . $transaction_id);
            
            // Try exact match first
            $stmt = $pdo->prepare("
                SELECT 
                    txid,
                    block_hash,
                    timestamp,
                    merkle_root,
                    input_addresses,
                    prev_txids,
                    output_addresses,
                    total_input,
                    total_output,
                    fees
                FROM tx_summary 
                WHERE block_hash = :search_value
                LIMIT 50
            ");
            
            $stmt->bindParam(':search_value', $transaction_id);
            $stmt->execute();
            $blockTxs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If exact match fails, try case-insensitive search
            if (empty($blockTxs)) {
                error_log("Exact block_hash match failed, trying case-insensitive search...");
                $stmt = $pdo->prepare("
                    SELECT 
                        txid,
                        block_hash,
                        timestamp,
                        merkle_root,
                        input_addresses,
                        prev_txids,
                        output_addresses,
                        total_input,
                        total_output,
                        fees
                    FROM tx_summary 
                    WHERE LOWER(block_hash) = LOWER(:search_value)
                    LIMIT 50
                ");
                
                $stmt->bindParam(':search_value', $transaction_id);
                $stmt->execute();
                $blockTxs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // If still no results, try partial match
            if (empty($blockTxs)) {
                error_log("Case-insensitive match failed, trying partial match...");
                $stmt = $pdo->prepare("
                    SELECT 
                        txid,
                        block_hash,
                        timestamp,
                        merkle_root,
                        input_addresses,
                        prev_txids,
                        output_addresses,
                        total_input,
                        total_output,
                        fees
                    FROM tx_summary 
                    WHERE block_hash LIKE :search_pattern
                    LIMIT 50
                ");
                
                $search_pattern = '%' . $transaction_id . '%';
                $stmt->bindParam(':search_pattern', $search_pattern);
                $stmt->execute();
                $blockTxs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            if (!empty($blockTxs)) {
                error_log("Found " . count($blockTxs) . " transactions by block_hash search");
                $mainTx = $blockTxs[0]; // Use first transaction as main
                
                // For block hash searches, return all transactions in that block
                $relatedTxs = $blockTxs;
                $processedTxs = array_column($blockTxs, 'txid');
            } else {
                error_log("No transactions found for block_hash: " . $transaction_id);
            }
        }
        
        // If still not found, try searching by input_addresses or output_addresses
        if (!$mainTx) {
            error_log("Not found by block_hash, trying address search...");
            $stmt = $pdo->prepare("
                SELECT 
                    txid,
                    block_hash,
                    timestamp,
                    merkle_root,
                    input_addresses,
                    prev_txids,
                    output_addresses,
                    total_input,
                    total_output,
                    fees
                FROM tx_summary 
                WHERE input_addresses LIKE :search_pattern OR output_addresses LIKE :search_pattern
                LIMIT 20
            ");
            
            $search_pattern = '%' . $transaction_id . '%';
            $stmt->bindParam(':search_pattern', $search_pattern);
            $stmt->execute();
            $addressTxs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($addressTxs)) {
                error_log("Found " . count($addressTxs) . " transactions by address search");
                $mainTx = $addressTxs[0]; // Use first transaction as main
                
                // For address searches, return all matching transactions
                $relatedTxs = $addressTxs;
                $processedTxs = array_column($addressTxs, 'txid');
            }
        }
        
        // If still not found, try partial match on txid
        if (!$mainTx) {
            error_log("Trying partial txid match search...");
            $stmt = $pdo->prepare("
                SELECT 
                    txid,
                    block_hash,
                    timestamp,
                    merkle_root,
                    input_addresses,
                    prev_txids,
                    output_addresses,
                    total_input,
                    total_output,
                    fees
                FROM tx_summary 
                WHERE txid LIKE :search_value
                LIMIT 1
            ");
            
            $search_pattern = '%' . $transaction_id . '%';
            $stmt->bindParam(':search_value', $search_pattern);
            $stmt->execute();
            $mainTx = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($mainTx) {
                error_log("Found with partial txid match: " . $mainTx['txid']);
            }
        }
        
        if (!$mainTx) {
            error_log("Transaction not found even with partial match");
            http_response_code(404);
            echo json_encode(['error' => 'Transaction not found']);
            return;
        }
        
        // Initialize related transactions if not already set (for block hash searches)
        if (!isset($relatedTxs)) {
            $relatedTxs = [$mainTx];
            $processedTxs = [$mainTx['txid']];
            
            // Get input transactions (where this tx's inputs come from)
            $prevTxIds = parseAddresses($mainTx['prev_txids']);
            if (!empty($prevTxIds)) {
                $placeholders = str_repeat('?,', count($prevTxIds) - 1) . '?';
                $stmt = $pdo->prepare("
                    SELECT txid, block_hash, timestamp, merkle_root, input_addresses, prev_txids, 
                           output_addresses, total_input, total_output, fees
                    FROM tx_summary 
                    WHERE txid IN ($placeholders)
                    LIMIT 10
                ");
                $stmt->execute($prevTxIds);
                $inputTxs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($inputTxs as $inputTx) {
                    if (!in_array($inputTx['txid'], $processedTxs)) {
                        $relatedTxs[] = $inputTx;
                        $processedTxs[] = $inputTx['txid'];
                    }
                }
            }
            
            // Get some transactions that might use this transaction's outputs
            $stmt = $pdo->prepare("
                SELECT txid, block_hash, timestamp, merkle_root, input_addresses, prev_txids, 
                       output_addresses, total_input, total_output, fees
                FROM tx_summary 
                WHERE prev_txids LIKE ?
                LIMIT 10
            ");
            $stmt->execute(['%' . $transaction_id . '%']);
            $outputTxs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($outputTxs as $outputTx) {
                if (!in_array($outputTx['txid'], $processedTxs)) {
                    $relatedTxs[] = $outputTx;
                    $processedTxs[] = $outputTx['txid'];
                }
            }
        } else {
            // For block hash searches, we already have all transactions from the block
            error_log("Block hash search - returning " . count($relatedTxs) . " transactions from block");
        }
        
        // Transform the data to match frontend format
        $transactions = array_map(function($row) {
            $input_addresses = parseAddresses($row['input_addresses']);
            $output_addresses = parseAddresses($row['output_addresses']);
            
            return [
                'id' => $row['txid'],
                'from' => $input_addresses,
                'to' => $output_addresses,
                'date' => formatTimestamp($row['timestamp']),
                'amount' => formatAmount($row['total_output']),
                'block_hash' => $row['block_hash'],
                'merkle_root' => $row['merkle_root'],
                'prev_txids' => parseAddresses($row['prev_txids']),
                'total_input' => $row['total_input'],
                'total_output' => $row['total_output'],
                'fees' => $row['fees']
            ];
        }, $relatedTxs);
        
        echo json_encode($transactions);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch transaction chain: ' . $e->getMessage()]);
    }
}

function getTransactionsByWallet($pdo, $wallet_address, $start_time = null, $end_time = null) {
    try {
        // Debug: Log the wallet search
        error_log("Searching for wallet address: " . $wallet_address);
        if ($start_time) error_log("Start time filter: " . $start_time);
        if ($end_time) error_log("End time filter: " . $end_time);
        
        // Build the SQL query with optional time filtering
        $sql = "
            SELECT DISTINCT txid, block_hash, timestamp, merkle_root, 
                   input_addresses, prev_txids, output_addresses, 
                   total_input, total_output, fees
            FROM tx_summary 
            WHERE (input_addresses ILIKE ? OR output_addresses ILIKE ?)
        ";
        
        $params = [];
        $searchPattern = '%' . $wallet_address . '%';
        $params[] = $searchPattern;
        $params[] = $searchPattern;
        
        // Add time filtering if provided
        if ($start_time) {
            $sql .= " AND timestamp >= ?";
            $params[] = strtotime($start_time);
        }
        
        if ($end_time) {
            $sql .= " AND timestamp <= ?";
            $params[] = strtotime($end_time);
        }
        
        $sql .= " ORDER BY timestamp ASC LIMIT 1000"; // Increased limit and changed to ASC for chronological order
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($transactions)) {
            echo json_encode(['error' => 'No transactions found for wallet address: ' . $wallet_address]);
            return;
        }
        
        // Process transactions for graph display
        $processedTransactions = [];
        foreach ($transactions as $tx) {
            $inputAddresses = parseAddresses($tx['input_addresses']);
            $outputAddresses = parseAddresses($tx['output_addresses']);
            $prevTxids = parseAddresses($tx['prev_txids']);
            
            $processedTransactions[] = [
                'id' => $tx['txid'],
                'from' => $inputAddresses,
                'to' => $outputAddresses,
                'prev_txids' => $prevTxids,
                'date' => date('Y-m-d H:i:s', $tx['timestamp']),
                'amount' => number_format((float)$tx['total_output'], 8) . ' BTC',
                'block_hash' => $tx['block_hash'],
                'fees' => number_format((float)$tx['fees'], 8) . ' BTC'
            ];
        }
        
        error_log("Found " . count($processedTransactions) . " transactions for wallet: " . $wallet_address);
        echo json_encode($processedTransactions);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch wallet transactions: ' . $e->getMessage()]);
    }
}

function parseAddresses($addressData) {
    if (empty($addressData)) return [];
    
    // Try to decode as JSON first
    if (is_string($addressData)) {
        $decoded = json_decode($addressData, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded; // Return all addresses, no limit
        }
        
        // If not JSON, try comma-separated values
        $addresses = array_filter(array_map('trim', explode(',', $addressData)));
        return $addresses; // Return all addresses, no limit
    }
    
    // If already an array
    if (is_array($addressData)) {
        return $addressData; // Return all addresses, no limit
    }
    
    return [$addressData];
}

function formatAmount($amount) {
    if (is_numeric($amount)) {
        // Format as satoshis with 7 decimal places: 37.0000000
        return sprintf('%.7f', $amount); // Display satoshis directly with 7 decimal places
    }
    return $amount;
}

function formatTimestamp($timestamp) {
    if (is_numeric($timestamp)) {
        // Convert Unix timestamp to local timezone (similar to Python's datetime.fromtimestamp)
        return date('Y-m-d H:i:s', $timestamp);
    }
    return $timestamp;
}
?>