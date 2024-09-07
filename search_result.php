<!DOCTYPE html>
<html>
<head>
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <?php
        require_once("module/check_date.php");
        $check_date = new CheckDate();
        $check_date->check_date();
        if ($check_date->flg_first_exec_today) {
            require_once("module/save_data.php");
            $save_event_info = new SaveEventInfo();
            $save_event_info->exec_scraping_py();
            $save_event_info->delete_database();
            $save_event_info->save();
            $save_event_info->disconnect();
        }

        require_once("module/get_print_event_info.php");
        $get_print_event_info = new GetSearchPrintEventInfo();   
        $get_print_event_info->get_event_info();
        $result = $get_print_event_info->extraction_by_search();
        if ($result == "success") {
            $get_print_event_info->print_event_info();
        }
        else {
            echo "検索に一致するイベントはありません";
        }
    ?>
</body>
</html>	