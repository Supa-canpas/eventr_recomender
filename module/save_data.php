<?php
    class SaveEventInfo {
        private $db;
        private $output;

        function __construct() {
            $database_name = 'root';
            $database_password = 'root';
            $this->db = new PDO('mysql:host=localhost;dbname=eventdatabase', $database_name, $database_password);
        }

        function exec_scraping_py() {
            $command="python scraping.py";
            exec($command, $this->output);
        }

        function delete_database() {
            try {
                // SQLクエリ作成
                $stmt = $this->db->prepare("DELETE FROM event_info;");
                // クエリ実行
                $res = $stmt->execute();
            }
            catch (PDOException) {
                echo 'データベース接続失敗';
            }
        }

        private function insert_database($title, $officialsite, $place, $date, $access, $category) {
            try {
                // SQLクエリ作成
                $stmt = $this->db->prepare("INSERT INTO event_info VALUES(?, ?, ?, ?, ?, ?);");
    
                $stmt->bindParam(1, $title,PDO::PARAM_STR);                
                $stmt->bindParam(2, $officialsite,PDO::PARAM_STR);
                $stmt->bindParam(3, $place, PDO::PARAM_STR);
                $stmt->bindParam(4, $date, PDO::PARAM_STR);
                $stmt->bindParam(5, $access, PDO::PARAM_STR);
                $stmt->bindParam(6, $category, PDO::PARAM_STR);
    
                // クエリ実行
                $res = $stmt->execute();
            }
            catch (PDOException) {
                echo 'データベース接続失敗';
            }
        }

        function disconnect() {
            // 切断
            $this->db = null;
        }

        function save() {
            $title = "";
            $officialsite = "";
            $place = "";
            $date = "";
            $access = "";
            $category = "";
            $i=0;

            foreach ($this->output as $_info) {
                $info = iconv("Shift-JIS", "UTF-8", $_info);

                if ($info == "separate_site") {
                    $this->insert_database($title, $officialsite, $place, $date, $access, $category);

                    $title = "";
                    $officialsite = "";
                    $place = "";
                    $date = "";
                    $access = "";
                    $i=0;
                } 
                else if ($info == "separate_table") {$i++;}
                else if ($info === "コラボカフェ" or $info === "ポップアップストア" or $info === "原画展・展示会") {$category = $info;}
                else {
                    if($i==0) $title .= $info;
                    else if($i==1) $officialsite .= $info;
                    else if($i==2) $place .= $info;
                    else if($i==3) $date .= $info;
                    else if($i==4) $access .= $info;
                }
            }
        }
    }
?>