    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/s.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/adminjava.js"></script></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

