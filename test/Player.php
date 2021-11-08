<?php
    //プレイヤーのコイン、手札と動作をクラスに
    class Player{
        public $c = 0; //コイン
        public $card = array(); //持ちカード
        public $p = 0; //持ち点

        //プレイヤーの情報を配列に格納
        public function getPlayer(){
            $i = array();
            $i["coin"] = $this->c;
            $i["pcard"] = $this->card;
            $i["point"] = $this->p;
            return $i;
        }

        //コインの枚数を上書き
        public function setCoin($coin){
            $this->c = $coin;
        }

        //山札から引く
        public function setCard($ar, $num){
            $a = array();
            for ($i=0; $i<$num; $i++) {
                $p = array_rand($ar);
                array_push($this->card, $ar[$p]);
                array_push($a, $p);
            }
            return $a;
        }

        public function resetPlayer(){
            $this->card = array();
            $this->p = 0;
        }
    }
?>