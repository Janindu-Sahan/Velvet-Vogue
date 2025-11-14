<?php
session_start();
include 'includes/db_connect.php';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Fetch order items
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?> - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        
        .invoice-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 60px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 3px solid #000;
        }
        
        .invoice-logo {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #000;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h1 {
            font-size: 36px;
            font-weight: 300;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 18px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .invoice-date {
            font-size: 14px;
            color: #666;
        }
        
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .detail-section h3 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 15px;
            color: #000;
        }
        
        .detail-section p {
            font-size: 14px;
            line-height: 1.8;
            color: #333;
            margin: 5px 0;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .invoice-table thead {
            background-color: #000;
            color: white;
        }
        
        .invoice-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 1px;
        }
        
        .invoice-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }
        
        .invoice-table tbody tr:last-child td {
            border-bottom: 2px solid #000;
        }
        
        .text-right {
            text-align: right;
        }
        
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        
        .summary-table {
            width: 300px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }
        
        .summary-row.total {
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: 700;
        }
        
        .invoice-footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 13px;
        }
        
        .invoice-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-print {
            background-color: #000;
            color: white;
        }
        
        .btn-print:hover {
            background-color: #333;
        }
        
        .btn-continue {
            background-color: #f0f0f0;
            color: #000;
        }
        
        .btn-continue:hover {
            background-color: #e0e0e0;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #c3e6cb;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .invoice-actions,
            .success-message {
                display: none;
            }
            
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 40px;
            }
        }
        
        @media (max-width: 768px) {
            .invoice-container {
                padding: 30px 20px;
            }
            
            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }
            
            .invoice-info {
                text-align: left;
            }
            
            .invoice-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .invoice-table {
                font-size: 12px;
            }
            
            .invoice-table th,
            .invoice-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-actions">
        <button onclick="window.print()" class="btn btn-print">🖨️ Print Invoice</button>
        <a href="orders.php" class="btn btn-continue">View All Orders</a>
        <a href="index.php" class="btn btn-continue">Continue Shopping</a>
    </div>

    <div class="invoice-container">
        <div class="success-message">
            ✓ Order placed successfully! Your order number is #<?php echo $order_id; ?>
        </div>
        
        <div class="invoice-header">
            <div class="invoice-logo">VELVET VOGUE</div>
            <div class="invoice-info">
                <h1>INVOICE</h1>
                <div class="invoice-number">Order #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></div>
                <div class="invoice-date">Date: <?php echo date('F d, Y', strtotime($order['order_date'])); ?></div>
            </div>
        </div>
        
        <div class="invoice-details">
            <div class="detail-section">
                <h3>BILL TO:</h3>
                <p><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <p>Email: <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($order['customer_phone']); ?></p>
            </div>
            
            <div class="detail-section">
                <h3>PAYMENT INFO:</h3>
                <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
                <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
            </div>
        </div>
        
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Size</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['size']); ?></td>
                    <td class="text-right">$<?php echo number_format($item['price'], 2); ?></td>
                    <td class="text-right"><?php echo $item['quantity']; ?></td>
                    <td class="text-right">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="invoice-summary">
            <div class="summary-table">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <div class="invoice-footer">
            <p><strong>VELVET VOGUE</strong></p>
            <p>Email: info@velvetvogue.com | Phone: +1 (555) 123-4567</p>
            <p style="margin-top: 15px;">Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
