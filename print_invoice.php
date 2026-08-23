<?php

declare(strict_types=1);

require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/permissions.php';
require_once __DIR__.'/includes/invoice_view_helpers.php';

requireRole([
    ROLE_ADMIN,
    ROLE_MANAGER,
    ROLE_CASHIER,
]);

$orderId = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    [
        'options' => [
            'min_range' => 1,
        ],
    ]
);

if ($orderId === false || $orderId === null) {
    http_response_code(400);
    exit('Invalid invoice reference.');
}

$orderStmt = $conn->prepare(
    'SELECT id,
        order_number, customer_name,
        total, created_at,
        status, payment_method,
        invoice_number, invoice_issued_at,
        vat_enabled_at_sale, vat_rate_at_sale,
        vat_amount, subtotal,
        business_name_at_invoice,
        business_address_at_invoice,
        business_vat_number_at_invoice FROM orders
        WHERE id = ? LIMIT 1'
);

if (!$orderStmt) {
    error_log(
        'print_invoice.php: order lookup prepare failed.'
    );

    http_response_code(500);
    exit('Unable to load invoice.');
}

if (!$orderStmt->bind_param('i', $orderId)) {
    $orderStmt->close();

    error_log('print_invoice.php: order lookup bind failed.');

    http_response_code(500);
    exit('Unable to load invoice.');
}

if (!$orderStmt->execute()) {
    $error = $orderStmt->error;

    $orderStmt->close();

    error_log('print_invoice.php: order lookup failed: '.$error);

    http_response_code(500);
    exit('Unable to load invoice.');
}

$orderResult = $orderStmt->get_result();

$order = $orderResult ? $orderResult->fetch_assoc() : null;

$orderStmt->close();

if (!$order) {
    http_response_code(404);
    exit('Order not found.');
}

$invoiceNumber = trim((string) ($order['invoice_number'] ?? ''));

if ($invoiceNumber === '') {
    http_response_code(404);
    exit('This order does not have an issued invoice.');
}

if ((string) $order['status'] !== 'Collected') {
    http_response_code(409);
    exit('An invoice is only available for a collected order.');
}

$businessName = trim((string) ($order['business_name_at_invoice'] ?? ''));
$businessAddress = trim((string) ($order['business_address_at_invoice'] ?? ''));
$businessVatNumber = trim((string) ($order['business_vat_number_at_invoice'] ?? ''));

if ($businessName === '' || $businessAddress === '') {
    http_response_code(409);

    exit(
        'This historical invoice does not contain the '
        .'required business snapshot. It cannot be '
        .'reproduced safely until the transaction is backfilled.'
    );
}

$vatEnabled = (int) $order['vat_enabled_at_sale'] === 1;
$vatRate = (float) $order['vat_rate_at_sale'];
$vatAmount = (float) $order['vat_amount'];
$subtotal = (float) $order['subtotal'];
$total = (float) $order['total'];

if ($vatEnabled && !preg_match('/^\d{10}$/', $businessVatNumber)) {
    http_response_code(409);

    exit('This VAT invoice is missing its stored VAT '.'registration number.');
}

$itemStmt = $conn->prepare(
    'SELECT product_name_at_sale,
        quantity, price,
        ROUND(quantity * price, 2) AS line_total FROM order_items
        WHERE order_id = ? ORDER BY id ASC'
);

if (!$itemStmt) {
    error_log('print_invoice.php: item lookup prepare failed.');

    http_response_code(500);
    exit('Unable to load invoice lines.');
}

if (!$itemStmt->bind_param('i', $orderId)) {
    $itemStmt->close();

    error_log('print_invoice.php: item lookup bind failed.');

    http_response_code(500);
    exit('Unable to load invoice lines.');
}

if (!$itemStmt->execute()) {
    $error = $itemStmt->error;

    $itemStmt->close();

    error_log(
        'print_invoice.php: item lookup failed: '
        .$error
    );

    http_response_code(500);
    exit('Unable to load invoice lines.');
}

$itemResult = $itemStmt->get_result();

if (!$itemResult) {
    $itemStmt->close();

    error_log(
        'print_invoice.php: item result unavailable.'
    );

    http_response_code(500);
    exit('Unable to load invoice lines.');
}

$items = [];

while ($row = $itemResult->fetch_assoc()) {
    $items[] = [
        'name' => trim(
            (string) (
                $row['product_name_at_sale'] ?? ''
            )
        ),
        'quantity' => (int) $row['quantity'],
        'price' => (float) $row['price'],
        'line_total' => (float) $row['line_total'],
    ];
}

$itemStmt->close();

if ($items === []) {
    http_response_code(409);
    exit('The invoice contains no recorded line items.');
}

foreach ($items as $item) {
    if ($item['name'] === '' || $item['quantity'] < 1 || $item['price'] < 0) {
        http_response_code(409);
        exit('The invoice contains invalid historical line data.');
    }
}

$paymentLabels = [
    'cash_pmt' => 'Cash',
    'card_pmt' => 'Card',
    'eft_pmt' => 'EFT',
];

$customerName = trim((string)
($order['customer_name'] ?? ''));

$paymentMethod = $paymentLabels[
(string) $order['payment_method']
] ?? 'Unspecified';

$invoiceTitle = $vatEnabled ? 'TAX INVOICE' : 'INVOICE';

$invoiceDate = $order['invoice_issued_at'] ?: $order['created_at'];

require __DIR__.'/invoice_print_view.php';
