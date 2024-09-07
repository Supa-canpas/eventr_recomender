<?php
    class CheckDate {
        public $flg_first_exec_today;

        function __construct() {
            $this->flg_first_exec_today = true;
        }

        function check_date() {
            try {
                $database_name = 'root';
                $database_password = 'root';
                $db = new PDO('mysql:host=localhost;dbname=eventdatabase', $database_name, $database_password);
                $stmt = $db->prepare("SELECT * FROM final_exec_date;");
                $res = $stmt->execute();
                if($res) {
                    $prev_date = $stmt->fetchAll();
                    if (count($prev_date) == 0) {
                        $this->flg_first_exec_today = true;
                    }
                    else {
                        $prev_date = $prev_date[0]["date"];
                        $today = date("Y-m-d");
                        if ($prev_date == $today) $this->flg_first_exec_today = false;
                        else $this->flg_first_exec_today = true;
                    }
                    
                    $stmt = $db->prepare("UPDATE final_exec_date SET date = ?;");
                    $stmt->bindParam(1, $today, PDO::PARAM_STR);
                    $res = $stmt->execute();
                }
    
                $db = null;
            }
            catch (PDOException) {
                echo 'データベース接続失敗';
            }
        }
    }
?>