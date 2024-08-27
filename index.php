<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <?php

        function database_process($_title,$_officialsite,$_place,$_date,$_access)
        {
                $user='root';
                $pass='root';
            try {
                
                $db = new PDO('mysql:host=localhost;dbname=eventdatabase', $user, $pass);
                
                $title=$_title;
                $officialsite=$_officialsite;
                $place=$_place;
                $date=$_date;
                $access=$_access;
    
                // SQLクエリ作成
                $stmt = $db->prepare("INSERT INTO event_info VALUES(?, ?, ?, ?, ?);");

                $stmt->bindParam(1, $title,PDO::PARAM_STR);                
                $stmt->bindParam(2, $officialsite,PDO::PARAM_STR);
                $stmt->bindParam(3, $place, PDO::PARAM_STR);
                $stmt->bindParam(4, $date, PDO::PARAM_STR);
                $stmt->bindParam(5, $access, PDO::PARAM_STR);
        
                // クエリ実行
                $res = $stmt->execute();
        
                // 切断
                $db = null;
            }
            catch (PDOException) {
                echo 'データベース接続失敗';
            }
        }

        $command="python scraping.py";
        exec($command, $output);

        $title = "";
        $officialsite = "";
        $place = "";
        $date = "";
        $access = "";

        $i=0;

        foreach ($output as $_info) {
            $info = iconv("Shift-JIS", "UTF-8", $_info);
            if ($info == "separate_site") {
                // echo "<hr>";
                database_process($title,$officialsite,$place,$date,$access);
                $i=0;
                $title = "";
                $officialsite = "";
                $place = "";
                $date = "";
                $access = "";
            } 
            else if ($info == "separate_table") {
                // echo "<br>";
                $i++;
            }
            else if ($info === "コラボカフェ" or $info === "ポップアップストア" or $info === "原画展・展示会") {
                // echo "<h1>";
                // echo $info;
                // echo "</h1>";
                // echo "<hr>";
            }
            else {
                // echo $info;

                if($i==0) $title.=$info;
                else if($i==1) $officialsite.=$info;
                else if($i==2) $place.=$info;
                else if($i==3) $date.=$info;
                else if($i==4) $access.=$info;
            }
        }

    ?>
</body>
</html>	