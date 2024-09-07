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
        require_once("module/search.php");
        $search = new SEARCH();
        $search->echo_search_screen("map.php");
    ?>

    <?php
        require_once("module/get_print_event_info.php");
        $get_print_event_info = new GetEventInfoMap();
        $get_print_event_info->get_event_info();
        $result = $get_print_event_info->extraction_by_search();
        $get_print_event_info->get_event_info_for_map();
    ?>

    <data id="latitude" value=""></data>
    <data id="altitude" value=""></data>
    <div id="map"></div>

    <script src="module/map.js"></script>
    <script>
        my_map = new MyMap("<?php echo str_replace('"', '', $get_print_event_info->str_event_info_datas)?>");
        my_map.exec_map_func(draw_map);
    </script>
</body>
</html>	