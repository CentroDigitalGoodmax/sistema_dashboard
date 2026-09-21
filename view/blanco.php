<?php include('../config/config.php'); ?>
<!doctype html>
<html lang="es">
<?php include('../layout/head.php'); ?>
<body class="bg-gray-100 font-sans antialiased">
<div class="flex h-screen">
    <?php include('../layout/sidebar.php'); ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include('../layout/header.php'); ?>
        <main class="flex-1 overflow-y-auto p-6">
            


        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>
<?php include('../layout/script.php'); ?>
</body>
</html>