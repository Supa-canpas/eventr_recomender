<?php
    class SEARCH {
        private $keyword;
        private $date;
        private $cb_cafe;
        private $cb_popup;
        private $cb_exhibition;

        function __construct() {
            $this->keyword = "";
            $this->date = "";
            $this->cb_cafe = "";
            $this->cb_popup = "";
            $this->cb_exhibition = "";

            if (isset($_POST["cb"]) == false and isset($_POST["date"]) == false and isset($_POST["search_keyword"]) == false) {
                $this->cb_cafe = "checked";
                $this->cb_popup = "checked";
                $this->cb_exhibition = "checked";
            }
            else {
                if (isset($_POST["search_keyword"])) $this->keyword = $_POST["search_keyword"];
                if (isset($_POST["date"])) $this->date = $_POST["date"];
                if (isset($_POST["cb"])) {
                    foreach($_POST["cb"] as $cb) {
                        if ($cb == "コラボカフェ") $this->cb_cafe = "checked";
                        else if ($cb == "ポップアップストア") $this->cb_popup = "checked";
                        else if ($cb == "原画展・展示会") $this->cb_exhibition = "checked";
                    }
                }
            }
        }

        function echo_search_screen($page_transition) {
            echo<<<SEARCH
                <link rel="stylesheet" href="search.css">
                <form class="search_screen" method="POST" action="?" name="search_form">
                    <p id="p_keyword">キーワード</p>
                    <input type="text" id="search_keyword" name="search_keyword" placeholder="イベント名、場所 など" value="{$this->keyword}">
                    <p id="p_date">日付</p>
                    <input type="date" id="date" name="date" value="{$this->date}" />
                    <p id="p_category">カテゴリ</p>
                    <input type="checkbox" id="cb_cafe" name="cb[]" value="コラボカフェ" {$this->cb_cafe} />
                    <label for="cb_cafe">コラボカフェ</label>
                    <br>
                    <input type="checkbox" id="cb_popup" name="cb[]" value="ポップアップストア" {$this->cb_popup} />
                    <label for="cb_popup">ポップアップストア</label>
                    <br>
                    <input type="checkbox" id="cb_exhibition" name="cb[]" value="原画展・展示会" {$this->cb_exhibition} />
                    <label for="cb_exhibition">原画展・展示会</label>
                    <br>
                    <button type="submit" id="btn_search" formaction="{$page_transition}">検索</button>
                </form>
            SEARCH;
        }
    }
?>