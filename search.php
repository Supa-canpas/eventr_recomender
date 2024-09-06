<?php
    class SEARCH {
        function echo_search_screen() {
            echo<<<SEARCH
                <link rel="stylesheet" href="search.css">
                <form class="search_screen" method="POST" action="?">
                    <p id="p_keyword">キーワード</p>
                    <input type="text" id="search_keyword" name="search_keyword" placeholder="イベント名、場所 など">
                    <p id="p_date">日付</p>
                    <input type="date" id="date" name="date" />
                    <p id="p_category">カテゴリ</p>
                    <input type="checkbox" id="cb_cafe" name="cb[]" value="コラボカフェ" checked />
                    <label for="cb_cafe">コラボカフェ</label>
                    <br>
                    <input type="checkbox" id="cb_popup" name="cb[]" value="ポップアップストア" checked />
                    <label for="cb_popup">ポップアップストア</label>
                    <br>
                    <input type="checkbox" id="cb_exhibition" name="cb[]" value="原画展・展示会" checked />
                    <label for="cb_exhibition">原画展・展示会</label>
                    <br>
                    <div class="button">
                        <button type="submit" id="btn_cancel" formaction="index.php">キャンセル</button>
                        <button type="submit" id="btn_search" formaction="search_result.php">検索</button>
                    </div>
                </form>
            SEARCH;
        }
    }

    $search = new SEARCH();
    $search->echo_search_screen();
?>