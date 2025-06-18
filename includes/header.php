<!-- includes/header.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Dokumenti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0; padding: 0;
        }
        .nav {
            background-color: #333;
            color: white;
            padding: 10px;
        }
        .nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }
        .container {
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #999;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        input[type="submit"], button {
            padding: 5px 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="/dokumenti/admin/dashboard.php">Admin Panel</a>
        <a href="/dokumenti/user/dashboard.php">Korisnik Panel</a>
        <a href="/dokumenti/logout.php">Logout</a>
    </div>
    <div class="container">
