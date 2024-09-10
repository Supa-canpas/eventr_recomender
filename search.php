<?php
    require_once("module/search.php");
    $search = new SEARCH();
    $search->echo_search_screen("search_result.php");
?>
<link rel="stylesheet" href="style/style.css">
<form method="POST" action="?">
    <div class="container">
        <button type="submit" formaction="index.php" class="item">ホーム</button>
        <button type="submit" formaction="map.php" class="item">マップ</button>
        <button type="submit" formaction="search.php" class="item">検索</button>
        <button type="submit" formaction="" class="item">並び替え</button>
    </div>
</form>  