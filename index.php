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
                        array_push($place, $buf);
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

            // 現在地にマーカーを立てる
            marker = new google.maps.Marker({
                position: MyLatLng,
                map: map,
                title: "現在地",
            });

            var place_strings = "<?php
                $return_string = "";
                foreach ($place as $p) {
                    $return_string .= $p . "__division__";
                }
                echo $return_string;
            ?>";
            var place_list = place_strings.split("__division__");
            var geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ

            for (let place of place_list) {
                console.log(place);
                geocoder.geocode({address: place}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {

                        var bounds = new google.maps.LatLngBounds();

                        for (var i in results) {
                            if (results[0].geometry) {
                                // 緯度経度を取得
                                var latlng = results[0].geometry.location;
                                // 住所を取得
                                var address = results[0].formatted_address;
                                // 検索結果地が含まれるように範囲を拡大
                                bounds.extend(latlng);
                                // マーカーのセット
                                marker = new google.maps.Marker({
                                    position: latlng,
                                    map: map,
                                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                                });
                                // マーカーへの吹き出しの追加
                                infoWindow = new google.maps.InfoWindow({
                                    content: "<a href='http://www.google.com/search?q=" + place + "' target='_blank'>" + place + "</a><br><br>" + latlng + "<br><br>" + address + "<br><br><a href='http://www.google.com/search?q=" + place + "&tbm=isch' target='_blank'>画像検索 by google</a>"
                                });
                                // マーカーにクリックイベントを追加
                                marker.addListener('click', function() {
                                    infoWindow.open(map, marker);
                                });
                            }
                        }
                    } else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                        console.log("見つかりません");
                    } else {
                        console.log(status);
                        console.log("エラー発生");
                    }
                });
            }
        };

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