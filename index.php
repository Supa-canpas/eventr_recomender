<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <?php
        $command="python scraping.py";
        exec($command, $output);
        foreach ($output as $_info) {
            $info = iconv("Shift-JIS", "UTF-8", $_info);
            if ($info == "separate_site") {
                echo "<hr>";
            } 
            else if ($info == "separate_table") {
                echo "<br>";
            }
            else if ($info === "コラボカフェ" or $info === "ポップアップストア" or $info === "原画展・展示会") {
                echo "<h1>";
                echo $info;
                echo "</h1>";
                echo "<hr>";
            }
            else {
                echo $info;
            }
        }
    ?>
</body>
</html>	