
<!DOCTYPE html>
<html lang = ja>

<html>
<head>
    <meta charset="utf-8" />
    <title>確認ページ</title>
    <style>
    </style>
</head>
<body>
<?php

    echo "DB情報など確認用";

    //接続
    $dsn = 'mysql:host=127.0.0.1;dbname=test;charset=utf8mb4';
    $user = 'kkikrym';
    $password = 'Kkikrym_5959';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    session_destroy();
    echo "<hr>";
    //コメントアウト解除でdb上書き
    /*
    $sql = 'DROP TABLE localdish;';
    $pdo->query($sql);
    */

    $data = "CREATE TABLE IF NOT EXISTS test"
    ." ("
    ."id int AUTO_INCREMENT PRIMARY KEY,"
    ."name VARCHAR(255) NOT NULL"
    .");";
    $create = $pdo->query($data);

    $sql = 'SHOW TABLES;';
    $result = $pdo->query($sql);
    echo "<hr>Current Databases：<br>";
    foreach ($result as $row){;
        echo $row[0];
        echo '<br>';
    }
    echo "<hr>";

    /*
    $sql = 'SHOW CREATE TABLE testbd';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
        echo $row[1];
        echo '<br>';
    }
    */

    /*
    $names = array();
    $users = array();
    foreach ($result as $row) {
        $user = array();
        if (!in_array($row["username"], $names)){
            $names[] = $row["username"];
            $user["name"] = $row["username"];
            $user["password"] = $row["password"];
            $user["commentnums"] = $row["commentnum"];
            $user["comment"] = $row["comment"];
        }else{

        }
    }

    */



?>
</body>
</html>