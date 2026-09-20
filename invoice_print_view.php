<?php
declare(strict_types=1);

/**
 * Presentation contract supplied by print_invoice.php.
 *
 * @var array{order_number: string, customer_name: ?string}                       $order
 * @var list<array{name: string, quantity: int, price: float, line_total: float}> $items
 * @var string                                                                    $customerName
 * @var string                                                                    $businessName
 * @var string                                                                    $businessAddress
 * @var string                                                                    $businessVatNumber
 * @var bool                                                                      $vatEnabled
 * @var float                                                                     $vatRate
 * @var float                                                                     $vatAmount
 * @var float                                                                     $subtotal
 * @var float                                                                     $total
 * @var string                                                                    $paymentMethod
 * @var string                                                                    $invoiceTitle
 * @var string                                                                    $invoiceNumber
 * @var string                                                                    $invoiceDate
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo invoiceHtml($invoiceTitle); ?> - <?php echo invoiceHtml($invoiceNumber); ?></title>
</head>
<body>

<div>
    <button type="button" onclick="window.print()">Print Invoice</button>
</div>

<main>
    <header>
        <div>
            <h1><?php echo invoiceHtml($businessName); ?></h1>
            <p><?php echo nl2br(invoiceHtml($businessAddress)); ?></p>

            <?php if ($vatEnabled) { ?>
                <p>
                    VAT registration:
                    <?php echo invoiceHtml($businessVatNumber); ?>
                </p>
            <?php } ?>
        </div>

        <div>
            <h2><?php echo invoiceHtml($invoiceTitle); ?></h2>
            <p>
                <?php echo invoiceHtml($invoiceNumber); ?>
            </p>
        </div>
    </header>

    <section aria-label="Invoice details">
        <div>
            <span>Invoice Date</span>
            <p><?php echo invoiceHtml($invoiceDate); ?></p>
        </div>

        <div>
            <span>Order Number</span>
            <p><?php echo invoiceHtml($order['order_number']); ?></p>
        </div>

        <div>
            <span>Customer</span>
            <p><?php echo invoiceHtml($customerName); ?></p>
        </div>
    </section>

    <table>
        <thead>
            <tr>
                <th scope="col">Item</th>
                <th scope="col">Qty</th>
                <th scope="col">Unit Price</th>
                <th scope="col">Amount</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo invoiceHtml($item['name']); ?></td>
                    <td><?php echo invoiceHtml((string) $item['quantity']); ?></td>
                    <td><?php echo invoiceMoney($item['price']); ?></td>
                    <td><?php echo invoiceMoney($item['line_total']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <section aria-label="Invoice totals">
        <div>
            <p>
                Payment method:
                <strong><?php echo invoiceHtml($paymentMethod); ?></strong>
            </p>
        </div>

        <table>
            <tbody>
                <tr>
                    <td>Subtotal</td>
                    <td><?php echo invoiceMoney($subtotal); ?></td>
                </tr>

                <?php if ($vatEnabled) { ?>
                    <tr>
                        <td>
                            VAT (<?php echo invoiceMoney($vatRate); ?>%)
                        </td>
                        <td><?php echo invoiceMoney($vatAmount); ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td>Total</td>
                    <td><?php echo invoiceMoney($total); ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <footer>
        This invoice is reproduced from the stored transaction and business snapshot
        associated with the order.
    </footer>
</main>

</body>
</html>