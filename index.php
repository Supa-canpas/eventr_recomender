<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
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
</body>
</html>	