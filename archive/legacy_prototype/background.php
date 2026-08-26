<?php
$pageTitle = "Document";
$currentPage = "background";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
    

    <div id="demo"></div>

    <script>

        // localStorage.setItem("color","#000");

        document.body.style.backgroundColor = localStorage.getItem("color");
    </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
