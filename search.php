<?php
    class SEARCH {
        function echo_search_screen() {
            echo<<<SEARCH
                <link rel="stylesheet" href="search.css">
                <div class="search_screen">
                    <p id="p_keyword">キーワード</p>
                    <input type="text" id="search_keyword" placeholder="イベント名、場所、日付 など">
                    <p id="p_category">カテゴリ</p>
                    <input type="checkbox" id="cb_cafe" name="cb_cafe" checked />
                    <label for="cb_cafe">コラボカフェ</label>
                    <br>
                    <input type="checkbox" id="cb_popup" name="cb_popup" checked />
                    <label for="cb_popup">ポップアップストア</label>
                    <br>
                    <input type="checkbox" id="cb_exhibition" name="cb_exhibition" checked />
                    <label for="cb_exhibition">原画展・展示会</label>
                    <br>
                    <div class="button">
                        <button id="btn_cancel">キャンセル</button>
                        <button id="btn_search">検索</button>
                    </div>
                </div>
            SEARCH;
        }
    }

    $search = new SEARCH();
    $search->echo_search_screen();
?>