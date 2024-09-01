<!DOCTYPE html>
<html>
<head>
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyD2SLN_jrwM5MCqTLAIcJcLkuORj5dLiPw&language=ja"></script>
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
        $str_event_info_datas = "";
        
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
                    
                    $str_event_info_datas .= "/packet=>";
                    $str_event_info_datas .= "/property=>";
                    $str_event_info_datas .= $event["title"];
                    $str_event_info_datas .= "/property=>";
                    $str_event_info_datas .= $event["officialsite"];
                    $str_event_info_datas .= "/property=>";

                    $placelist=explode('、',$event["place"]);
                    foreach( $placelist as $buf ){
                        $str_event_info_datas .= "__division__=>";
                        $str_event_info_datas .= $buf;
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

            setTimeout(() =>{
                // 現在地にマーカーを立てる
              marker = new google.maps.Marker({
                  position: MyLatLng,
                  map: map,
                  title: "現在地",
              });
              infoWindow = new google.maps.InfoWindow({
                  content: "現在地"
              });
              infoWindow.open(map, marker);
            }, 1500)

            

            var str_event_info_datas = "<?php echo $str_event_info_datas; ?>";
            var event_info_packets = str_event_info_datas.split("/packet=>").slice(1);
            for (let i = 0; i < event_info_packets.length; i++) {
              let event_datas = event_info_packets[i].split("/property=>").slice(1);
              let place_list = event_datas[2].split("__division__=>").slice(1);
              event_info_packets[i] = {title: event_datas[0], officialsite: event_datas[1], places: place_list};
            }
            
            var geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
            
            for (let event_info_packet of event_info_packets) {
              for (let place of event_info_packet.places) {
                  geocoder.geocode({address: place}, function(results, status) {
                      if (status == google.maps.GeocoderStatus.OK) {
                          for (var i in results) {
                              if (results[0].geometry) {
                                  // 緯度経度を取得
                                  var latlng = results[0].geometry.location;
                                  // 住所を取得
                                  var address = results[0].formatted_address;
                                  // マーカーのセット
                                  marker = new google.maps.Marker({
                                      title: event_info_packet.title,
                                      position: latlng,
                                      map: map,
                                      icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                                  });
                                  // マーカーへの吹き出しの追加
                                  infoWindow = new google.maps.InfoWindow({
                                      content: "<a href='" + event_info_packet.officialsite + "'>" + event_info_packet.title + "</a>"
                                  });
                                  infoWindow.open(map, marker);
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
            }
        };

        my_map = new MyMap(draw_map);
        my_map.exec_map_func();
    </script>
</body>
</html>	