    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/script.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

