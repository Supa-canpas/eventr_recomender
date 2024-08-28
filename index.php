<!DOCTYPE html>
<html>
<head>]
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        require_once("save_data.php");
        $save_event_info = new SaveEventInfo();
        $save_event_info->exec_scraping_py();
        $save_event_info->delete_database();
        $save_event_info->save();
        $save_event_info->disconnect();
    ?>

    <?php
        try {
            $database_name = 'root';
            $database_password = 'root';
            $db = new PDO('mysql:host=localhost;dbname=eventdatabase', $database_name, $database_password);
            // SQLクエリ作成
            $stmt = $db->prepare("SELECT * FROM event_info;");
            // クエリ実行
            $res = $stmt->execute();
            if($res) {
                $event_info = $stmt->fetchAll();
                foreach($event_info as $event) {
                    echo <<<EVENT
                    <div class="event_info">
                        <p class="title">{$event["title"]}</p>
                        <p>{$event["date"]}</p>
                        <p>{$event["place"]}</p>
                        <p><a href="{$event["officialsite"]}">公式サイト</a></p>
                    </div>
                    EVENT;
                }

            }
        }
        catch (PDOException) {
            echo 'データベース接続失敗';
        }
    ?>
</body>
</html>	