<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <!-- FONT -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret shared</title>
</head>
<body>
    <main>
        <h1>Secret shared!</h1>
        <button onclick="document.location='/v1'">Home page</button>
            <?php
                $hash = $_POST['hash'];
                echo $hash . '<br />';
                echo 'Secret: ' . $_POST["secret"] . '<br />';
                echo 'Expview: ' . $_POST["expview"] . '<br />';
                echo 'TTL: ' . $_POST["ttl"] . '<br />';
            ?>
    </main>
</body>
</html>