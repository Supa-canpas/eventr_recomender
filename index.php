<!DOCTYPE html>
<html>
<head>
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyD2SLN_jrwM5MCqTLAIcJcLkuORj5dLiPw&language=ja"></script>
    <style>
        html { height: 100% }
        body { height: 100% }
        #map { height: 100%; width: 100%}
    </style>
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
        class MyMap {
            latitude;
            altitude;
            cnt_process_using_latitude_longitude;
            func_process_using_latitude_longitude;

            constructor() {
                this.cnt_process_using_latitude_longitude = 0;
                this.func_process_using_latitude_longitude = () => {};
            }

            get_latitude_altitude(position) {
                document.getElementById("latitude").innerHTML = position.coords.latitude;  // 緯度
                document.getElementById("altitude").innerHTML = position.coords.longitude; // 経度
            }

            get_postion_and_exec_map_process() {
                let timeout = 1000;

                navigator.geolocation.getCurrentPosition(this.get_latitude_altitude, function(e){console.log(e.message);}, {"enableHighAccuracy": true, "timeout": timeout, "maximumAge": 1000});
                setTimeout(()=>{
                    this.latitude = document.getElementById("latitude").innerHTML;
                    this.altitude = document.getElementById("altitude").innerHTML;
                    if (this.latitude == "" && this.altitude == "") this.get_postion_and_exec_map_process();
                    this.process_using_latitude_longitude();
                }, timeout);
            }

            process_using_latitude_longitude() {
                if (this.cnt_process_using_latitude_longitude == 0) {
                    this.func_process_using_latitude_longitude();
                }
                this.cnt_process_using_latitude_longitude = 1;
            }
        }
    </script>

    <div id="map"></div>

    <script>
        let my_map = new MyMap();

        const draw_map = () => {
            var MyLatLng = new google.maps.LatLng(my_map.latitude, my_map.altitude);
            var Options = {
            zoom: 15,      //地図の縮尺値
            center: MyLatLng,    //地図の中心座標
            mapTypeId: 'roadmap'   //地図の種類
            };
            var map = new google.maps.Map(document.getElementById('map'), Options);
        }

        my_map.func_process_using_latitude_longitude = draw_map;

        my_map.get_postion_and_exec_map_process();
    </script>
</body>
</html>	