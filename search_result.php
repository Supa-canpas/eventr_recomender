<!DOCTYPE html>
<html>
<head>
    <title>イベントを君におすすめするWebサイト</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
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