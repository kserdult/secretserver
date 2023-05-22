<?php class Database {
//Data for database connection
public static $host = "localhost";
public static $dbName = "*****";
public static $username = "*****";
public static $password = "*****";

private static function connect() {
    $pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$dbName.";charset=utf8", self::$username, self::$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

//Creation Query function
public static function getQuery($query, $params = array()) {
    $statement = self::connect()->prepare($query);
    $statement->execute(($params));

    if (explode(' ', $query)[0] =='SELECT') {
        $data = $statement->fetchAll();
        return $data;
    }
}

//Handling the expirity after certain amount of views
public static function setExpAfterViews($hash){
    self::getQuery('UPDATE secret SET expireAfterViews=expireAfterViews-1 WHERE hash="'.$hash.'";');
}


public static function postQuery($query, $params = array()) {
    $statement = self::connect()->prepare($query);
    $statement->execute(($params));

    if (explode(' ', $query)[0] =='SELECT') {
        $data = $statement->fetchAll();
        return $data;
    }
}

//handling the deletion of expired data
public static function Delete (){
    //self::getQuery("DELETE FROM secret WHERE createdAt < CURRENT_TIMESTAMP - INTERVAL expireAfter MINUTE OR expireAfterViews <= 0;");
    self::getQuery("DELETE FROM secret WHERE (createdAt <= DATE_SUB(NOW(), INTERVAL expireAfter MINUTE) && expireAfter!=0) OR expireAfterViews <= 0;");
}
}
Database::Delete(); ?>