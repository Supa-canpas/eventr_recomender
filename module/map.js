// function get_google_map_api() {
//     <script>
//         let google_map_api = document.querySelector("#google_map_api");
//         google_map_api.setAttribute("src", "http://maps.google.com/maps/api/js?key=AIzaSyD2SLN_jrwM5MCqTLAIcJcLkuORj5dLiPw&language=ja");
//     </script>


//     var req = new XMLHttpRequest();
//     req.onreadystatechange = function() {
//         var google_map_api = document.getElementById('google_map_api');
//         if (req.readyState == 4) { // 通信の完了時
//         if (req.status == 200) { // 通信の成功時
//             google_map_api.setAttribute("src", req.responseText);
//         }
//         }else{}
//     }
//     req.open('POST', 'send_google_map_api.php', true);
//     req.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
// }

class MyMap {
    latitude;
    altitude;
    event_info_packets;

    constructor(str_event_info_datas) {        
        this.event_info_packets = str_event_info_datas.split("/packet=>").slice(1);
        for (let i = 0; i < this.event_info_packets.length; i++) {
            let event_datas = this.event_info_packets[i].split("/property=>").slice(1);
            let place_list = event_datas[2].split("__division__=>").slice(1);
            this.event_info_packets[i] = {title: event_datas[0], officialsite: event_datas[1], places: place_list};
        }
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

    async exec_map_func(draw_map) {
        try {
            await this.get_latitude_and_altitude();
            this.latitude = document.getElementById("latitude").value;
            this.altitude = document.getElementById("altitude").value;
            draw_map(this.latitude, this.altitude, this.event_info_packets);
            
        } catch(err) {
            console.log(err)
        }
    }
}

let draw_map = (latitude, altitude, event_info_packets) => {
    // マップ定義
    var MyLatLng = new google.maps.LatLng(latitude, altitude);
    var Options = {
        zoom: 15,      //地図の縮尺値
        center: MyLatLng,    //地図の中心座標
        mapTypeId: 'roadmap'   //地図の種類
    };
    var map = new google.maps.Map(document.getElementById('map'), Options);

    // ユーザーのマーカー設置
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

    // イベントのマーカーの設置
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