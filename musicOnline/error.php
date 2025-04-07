<?php session_start(); ?>
<?php if (isset($_SESSION['error'])): ?>
    <script>
        alert("<?php echo htmlspecialchars($_SESSION['error']); ?>");
        window.location.href = "login.php"; // redirect to login.php
    </script>
<?php endif; ?>

