<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AOB Legal Repository</title>

    <link rel="stylesheet" href="/aob_repository/assets/css/style.css">

</head>


<body>


<header>

    <div>

        <h2>
            Audit Oversight Board Pakistan
        </h2>

    </div>


    <div>

        Welcome,
        <?php
        echo htmlspecialchars(
            $_SESSION['username'] ?? 'User'
        );
        ?>

    </div>

</header>
