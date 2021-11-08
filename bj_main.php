<?php
//ボタンが押されたら同じページにリダイレクト・瞬時に結果を反映
    if (isset($_POST["reset"]) or isset($_POST["hit"]) or isset($_POST["stand"]) or isset($_POST["nextgame"])) {
        header("Location: " . $_SERVER['PHP_SELF']);
    }
    session_start();
    $fpath=dirname(__FILE__); //ファイルのpath
    require_once $fpath."/bj_head.txt"; //head読み込み
?>

<body><div class="main">

<?php
//クラス・関数ファイル読み込み
    require_once $fpath."/bj_functions.php";
    require_once $fpath."/Player.php";

//リセットボタン、開発途中に使用
///*
    echo
    '<form method="post" action="" >
        <input type="hidden" name="reset">
        <input type=submit value="RESET">
    </form>';
    if(isset($_POST["reset"])){
        session_destroy();
        $_SESSION["start"]=false;
    }
    echo $_SESSION["game"]."ゲーム目<br>";
    echo $_SESSION["n"]."人<br>";
    echo $_SESSION["pturn"]."<br>";
//*/

/* 以下ゲーム内メイン処理 */

//2ターン目以降
    if($_SESSION["start"]){
        $n = $_SESSION["n"];
        if (!isset($_SESSION["pturn"])) { //操作するプレイヤーの番号を設定(pturn)
            $_SESSION["pturn"] = 1;
        }
        if (!isset($_SESSION["game"])) {
            $_SESSION["game"] = 1;
        }

    //ゲーム終了直後にプレイヤーにカードをセットし直す
        if (isset($_SESSION["gameend"])){
            $deck = $_SESSION["deck"];
            for ($i=1; $i<=$n; $i++) {
                $name = "p".$i;
                $player = unserialize($_SESSION[$name]);
                $player->resetPlayer();
                $a = $player->setCard($deck, 2);
                foreach ($a as $item) {
                    unset($deck[$item]);
                }
                $_SESSION[$name] = serialize($player);
            }
            unset($_SESSION["gameend"]);
            $_SESSION["deck"] = $deck;
        }

    //ディーラーの処理
        $dealer = unserialize($_SESSION["dealer"]);
        if($_SESSION["pturn"]>$n){
            $deck = $_SESSION["deck"];
            $a = $dealer->setCard($deck, 1);
            unset($deck[$a[0]]);
            $_SESSION["dealer"] = serialize($dealer);
            $_SESSION["deck"] = $deck;
            $dinfo = $dealer->getPlayer();
            echo '<hr>Dealer: ';
            foreach ($dinfo["pcard"] as $c) {
                echo $c." ";
            }
            echo "<hr>";

        //各パラメーターリセット
            $_SESSION["game"] += 1;
            unset($_SESSION["pturn"]);
            $deck = resetcards();
            $dealer->resetPlayer();
            $a = $dealer->setCard($deck,2);
            unset($deck[$a[0]]);
            $_SESSION["deck"] = $deck;
            $_SESSION["dealer"] = serialize($dealer);

            echo
            '<form method="post" action="">
                <button type=submit name="nextgame" value="">次のゲームへ</button>
            </form>';
            $_SESSION["gameend"] = "true";
        }else{
            $dinfo = $dealer->getPlayer();
            echo '<hr>Dealer: '.$dinfo["pcard"][0]." ??<hr>";
        }

    //各プレイヤーの処理
        $ifstand = false;
        for ($i=1; $i<=$n; $i++) {
            //プレイヤー情報表示
                $name = "p".$i;
                echo "<hr>".$name."<br>コイン： ";
                $player = unserialize($_SESSION[$name]);
                $info = $player->getPlayer();
                echo $info["coin"];
                echo "枚<br>デッキ： ";
                foreach ($info["pcard"] as $c) {
                    echo $c." ";
                }

            //プレイヤーごとにHIT,STANDができるようにする
                if($_SESSION["pturn"] == $i){
                        echo
                        '<form method="post" action="">
                            <button type=submit name="hit" value="">HIT</button>
                            <button type=submit name="stand" value="">STAND</button>
                        </form>';

                    //HITの処理
                        if(isset($_POST["hit"])){
                            //デッキ読み込み、手札を引く、セッションに保存
                                $deck = $_SESSION["deck"];
                                foreach ($deck as $d){
                                    echo $d;
                                }
                                $a = $player->setCard($deck, 1);
                                unset($deck[$a[0]]);
                                $_SESSION[$name] = serialize($player);
                                $_SESSION["deck"] = $deck;

                    //STANDの処理
                        }elseif(isset($_POST["stand"])){
                            $ifstand = true;
                        }
                }
            echo "<hr>";
        }
        if ($ifstand) {
            $_SESSION["pturn"] += 1; //次のプレイヤーへ
        }

//最初のターンの処理、初期設定etc
    }elseif(!$_SESSION["start"]) {

        if (isset($_POST["n"])) {
            //プレイヤー数、初期コイン、山札を設定
                $_SESSION["n"] = $_POST["n"];
                $n = $_SESSION["n"];
                $coin_ini = 100;
                $deck = resetcards();
            //n人分のプレイヤーとディーラーを生成
                $dealer= new Player();
                $a = $dealer->setCard($deck, 2);
                foreach ($a as $item){
                    unset($deck[$item]);
                }
                $dinfo=$dealer->getPlayer();
                echo '<hr>Dealer: '.$dinfo["pcard"][0]." ??<hr>";
                $_SESSION["dealer"] = serialize($dealer);
                for ($i=1; $i<$n+1; $i++) {
                        $pname = "p".$i;
                        $$pname = new Player();
                    //コインを配る
                        $$pname->setCoin($coin_ini);
                    //カードを配る
                        $a = $$pname->setCard($deck, 2);
                        foreach ($a as $item){
                            unset($deck[$item]);
                        }
                    //情報表示
                        echo "<hr>".$pname."<br>コイン： ";
                        $info = $$pname->getPlayer();
                        echo $info["coin"];
                        echo "枚<br>デッキ： ";
                        foreach ($info["pcard"] as $c) {
                            echo $c." ";
                        }
                        echo "<hr>";
                    //セッションに情報を保存
                        $_SESSION[$pname] = serialize($$pname);
                }
            $_SESSION["deck"] = $deck;
            $_SESSION["start"] = true;
            echo '<form method="post" action=""><br><input type="submit" value="ゲーム開始"></form>';
        }else{
            //プレイ人数を選択 四人まで
                $nnum = array("1","2","3","4");
                echo 'プレイ人数を選択して下さい：<br><form method="post" action="">プレイ人数：<select name="n" value="">';
                echo '<option value="" selected></option>';
                foreach ($nnum as $n) {
                    echo '<option value="'.$n.'">'.$n.'</option>';
                }
                echo '</select><br><input type="submit" value="ゲーム開始"></form>';
        }
    }
?>

</div></body>
</html>