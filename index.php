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
        require_once("save_data.php");
        $save_event_info = new SaveEventInfo();
        $save_event_info->exec_scraping_py();
        $save_event_info->delete_database();
        $save_event_info->save();
        $save_event_info->disconnect();
    ?>

    <?php
        $place=[];
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
                    $placelist=explode('、',$event["place"]);
                    foreach( $placelist as $buf ){
                        array_push($place,$buf);
                    }
                }

            }
        }
        catch (PDOException) {
            echo 'データベース接続失敗';
        }
    ?>

    <data id="latitude" value=""></data>
    <data id="altitude" value=""></data>
    <script>
        class MyMap {
            latitude;
            altitude;
            draw_map;

            constructor(draw_map) {
                this.draw_map = draw_map;
            }

            get_latitude_and_altitude() {
                return new Promise( (resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            document.getElementById("latitude").value = position.coords.latitude;  // 緯度
                            document.getElementById("altitude").value = position.coords.longitude; // 経度
                            resolve()
                        },
                        (error) => {
                            reject(error);
                        }
                    )
                });
            }

            async exec_map_func() {
                try {
                    await this.get_latitude_and_altitude();
                    this.latitude = document.getElementById("latitude").value;
                    this.altitude = document.getElementById("altitude").value;
                    this.draw_map(this.latitude, this.altitude);
                    
                } catch(err) {
                    console.log(err)
                }
            }
        }
    </script>

    <div id="map"></div>

    <script>
        let draw_map = (latitude, altitude) => {
            var MyLatLng = new google.maps.LatLng(latitude, altitude);
            var Options = {
                zoom: 15,      //地図の縮尺値
                center: MyLatLng,    //地図の中心座標
                mapTypeId: 'roadmap'   //地図の種類
            };
            var map = new google.maps.Map(document.getElementById('map'), Options);
        }

        my_map = new MyMap(draw_map);
        my_map.exec_map_func();
    </script>

    <?php
        foreach($place as $buf)
        {
            echo $buf."<br/>";
        }
    ?>
</body>
</html>	