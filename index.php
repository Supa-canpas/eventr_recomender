<!DOCTYPE html>
<html>
<head>
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script>
    function test() {
        navigator.geolocation.getCurrentPosition(test2);
    }

    function test2(position) {
        var geo_text = "緯度:" + position.coords.latitude + "\n";
        geo_text += "経度:" + position.coords.longitude + "\n";
        geo_text += "高度:" + position.coords.altitude + "\n";
        geo_text += "位置精度:" + position.coords.accuracy + "\n";
        geo_text += "高度精度:" + position.coords.altitudeAccuracy  + "\n";
        geo_text += "移動方向:" + position.coords.heading + "\n";
        geo_text += "速度:" + position.coords.speed + "\n";

        var date = new Date(position.timestamp);

        geo_text += "取得時刻:" + date.toLocaleString() + "\n";

        alert(geo_text);
    }
    </script>
</head>
<body>
    <button onclick="test()">test</button>
    <!-- <?php
        require_once("save_data.php");
        $save_event_info = new SaveEventInfo();
        $save_event_info->exec_scraping_py();
        $save_event_info->delete_database();
        $save_event_info->save();
        $save_event_info->disconnect();
    ?> -->

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