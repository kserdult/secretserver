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
    <title>Share your Secret</title>
</head>
<body>
    <?php
        function rndString(){
            return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1, 10);
        }
        $hash=rndString();
    ?>
    <main>
        <h1>Share your secret</h1>
        <button onclick="document.location='/v1'">Home page</button>
        <form action="/v1/secretpost" method="post">
                Secret: <input type="text" name="secret" /> <br />
                Expire after X views: <input type="number" name="expview" /> <br />
                Time To Live (min): <input type="number" name="ttl" /> <br />
                <?php
                    echo "<input type='hidden' name='hash' value='$hash'/>"
                ?>
                <input type="Submit" value="Submit">
        </form>
    </main>
</body>
</html>