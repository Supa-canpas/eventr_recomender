<!DOCTYPE html>
<html>
<head>
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <?php
        // require_once("save_data.php");
        // $save_event_info = new SaveEventInfo();
        // $save_event_info->exec_scraping_py();
        // $save_event_info->delete_database();
        // $save_event_info->save();
        // $save_event_info->disconnect();
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

    <p id="latitude"></p>
    <p id="altitude"></p>
    <script>
        class UserPostion {
            latitude;
            altitude;

            get_latitude_altitude(position) {
                // UserPostion.latitude = position.coords.latitude;  // 緯度
                // UserPostion.altitude = position.coords.longitude; // 経度
                let div_latitude = document.getElementById("latitude");
                let div_altitude = document.getElementById("altitude");

                div_latitude.innerHTML = position.coords.latitude;  // 緯度
                div_altitude.innerHTML = position.coords.longitude; // 経度
            }

            get_postion() {
                navigator.geolocation.getCurrentPosition(this.get_latitude_altitude);

                let div_latitude = document.getElementById("latitude");
                let div_altitude = document.getElementById("altitude");

                // this.latitude = div_latitude.innerHTML;  // 緯度
                // this.altitude = div_altitude.innerHTML; // 経度
                console.log(div_latitude.innerHTML);
            }
        }
        
        let user_postion = new UserPostion();
        user_postion.get_postion();

        let latitude;
        let altitude

        setTimeout(() => {
            let div_latitude = document.getElementById("latitude");
            let div_altitude = document.getElementById("altitude");

            // this.latitude = div_latitude.innerHTML;  // 緯度
            // this.altitude = div_altitude.innerHTML; // 経度
            console.log(div_latitude.innerHTML);
            console.log(div_altitude.innerHTML);

            latitude = div_latitude.innerHTML;
            altitude = div_altitude.innerHTML;

            // console.log(user_postion.latitude, user_postion.altitude)
        }, 1000);

        
        
    </script>

    <script type="text/javascript" charset="utf-8" src="https://map.yahooapis.jp/js/V1/jsapi?appid=dj00aiZpPTdueDAyc3RReGRMTSZzPWNvbnN1bWVyc2VjcmV0Jng9MDc">
        // console.log(latitude, altitude)
        let ymap = new Y.Map("map");
        setTimeout(() => {
            ymap.drawMap(new Y.LatLng(latitude, altitude), 17, Y.LayerSetId.NORMAL);
        }, 1000)
    </script>

</body>
</html>	