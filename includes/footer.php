<?php

if (isset($loadChart) && $loadChart) { ?>

    <script src="assets/js/chart.umd.min.js"></script>

    <script>
        window.chartLabels = <?php echo json_encode($chartLabels ?? []); ?>;

        window.chartData = <?php echo json_encode($chartData ?? []); ?>;
    </script>

<?php } ?>

<?php if (isset($loadScript) && $loadScript) { ?>

    <script src="assets/js/script.js"></script>

<?php } ?>
<script src="assets/js/clock.js"></script>
</body>

</html>