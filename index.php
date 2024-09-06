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
        require_once("module/get_print_event_info.php");
        $get_print_event_info = new GetPrintEventInfoForIndexPHP();
        $get_print_event_info->get_event_info();
        $get_print_event_info->print_event_info();
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
                            resolve();
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
                marker.addListener("click", function() {
                    infoWindow.open(map, marker);
                });
                infoWindow.open(map, marker);
            }, 1500)

        
            var str_event_info_datas = "<?php echo str_replace('"', '', $get_print_event_info->str_event_info_datas)?>";
            var event_info_packets = str_event_info_datas.split("/packet=>").slice(1);
            for (let i = 0; i < event_info_packets.length; i++) {
                let event_datas = event_info_packets[i].split("/property=>").slice(1);
                let place_list = event_datas[2].split("__division__=>").slice(1);
                event_info_packets[i] = {title: event_datas[0], officialsite: event_datas[1], places: place_list};
            }
            
            var geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
            
            for (let event_info_packet of event_info_packets) {
                for (let place of event_info_packet.places) {
                    place = place.replace(/<a[^>]*>/g, "").replace("</a>", "");
                    geocoder.geocode({address: place}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0].geometry) {
                                // 緯度経度を取得
                                var latlng = results[0].geometry.location;
                                // 住所を取得
                                var address = results[0].formatted_address;

                                var infoWindow = new google.maps.InfoWindow({
                                    content: "<a href='" + event_info_packet.officialsite + "'>" + event_info_packet.title + "</a>"
                                });
                                var marker = new google.maps.Marker({
                                    title: event_info_packet.title,
                                    position: latlng,
                                    map: map,
                                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                                });
                                infoWindow.open(map, marker);
                                marker.addListener("click", function() {
                                    infoWindow.open(map, marker);
                                });
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