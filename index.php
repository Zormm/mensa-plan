<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/main.css">
    <link rel="stylesheet" href="public/css/tailwind.css">
    <title>Menüplan</title>
</head>
<body class="bg-gray-100">
<section class="max-w-7xl mx-auto flex flex-col gap-4">
    <section class="bg-primary p-4 rounded-b-xl">
        <h1 class="text-2xl">Menüplan</h1>
    </section>
<?php
require 'menu.php';
require 'map.php';
?>
</section>
</body>
</html>
