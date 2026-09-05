<?php /** @var array $row */ ?>

    <tr>
        <td>
            <a href="order_details.php?id=<?php echo (int) $row['id']; ?>" class="order-link">
                <?php echo htmlspecialchars($row['order_number'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </td>

        <td>
            <?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8'); ?>
        </td>

        <td>R <?php echo number_format($row['total'], 2); ?>
        </td>

        <td>
            <span class="status <?php
                switch ($row['status']) {
                    case 'Pending': echo 'pending';
                        break;

                    case 'Preparing': echo 'info';
                        break;

                    case 'Ready': case 'Collected': echo 'success';
                        break;

                    case 'Cancelled': echo 'danger';
                        break;

                    default: echo 'info';
                }
            ?>">
                <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </td>

        <td>
            <?php switch ($row['payment_method']) {
                case 'cash_pmt':
                    echo '💵 Cash';
                    break;

                    case 'card_pmt':
                        echo '💳 Card';
                        break;

                        case 'eft_pmt':
                            echo '🏦 EFT';
                            break;

            default: echo '-';
        }
                ?>
        </td>

        <td>
            <?php echo date('d F Y H:i', strtotime($row['created_at'])); ?>
        </td>

        <td>
            <?php renderOrderAction($row); ?>
        </td>

    </tr>
