<div class="report-actions">
    <div class="chart-filter">
        <a href="?range=7" class="action-btn <?php echo $range === '7' ? 'active-nav' : ''; ?>">7 Days</a>
        <a href="?range=30" class="action-btn <?php echo $range === '30' ? 'active-nav' : ''; ?>">30 Days</a>
        <a href="?range=month" class="action-btn <?php echo $range === 'month' ? 'active-nav' : ''; ?>">This Month</a>
        <a href="?range=year" class="action-btn <?php echo $range === 'year' ? 'active-nav' : ''; ?>">This Year</a>
    </div>

    <div class="report-buttons">

        <a href="export_report_csv.php?range=<?php echo urlencode($range); ?>" class="action-btn">
            Export CSV
        </a>

        <a href="export_report_pdf.php?range=<?php echo urlencode($range); ?>"
            target="_blank"
            class="action-btn">
            Export PDF
        </a>

        <a href="#" onclick="window.print();" class="action-btn">
            Print
        </a>

    </div>
</div>
