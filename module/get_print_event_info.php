<?php
    class GetEventInfo {
        public $event_info;

        function __construct() {
            $this->event_info = [];
        }

        protected function add_get_event_info($event, $cnt) {}

        function get_event_info() {
            try {
                $database_name = 'root';
                $database_password = 'root';
                $db = new PDO('mysql:host=localhost;dbname=eventdatabase', $database_name, $database_password);
                $stmt = $db->prepare("SELECT * FROM event_info;");
                $res = $stmt->execute();
                if($res) {
                    $event_info = $stmt->fetchAll();
                    $cnt = 0;
                    foreach($event_info as $event) {
                        array_push($this->event_info, []); 
                        $key_name = ["title", "officialsite", "place", "date", "access", "category"];
                        foreach ($key_name as $key) $this->event_info[$cnt][$key] = $event[$key];
                        $this->add_get_event_info($event, $cnt);         
                        $cnt++;
                    }
                }

                $db = null;
            }
            catch (PDOException) {
                echo 'データベース接続失敗';
            }
        }
    }

    class GetPrintEventInfo extends GetEventInfo {
        function print_event_info() {
            foreach ($this->event_info as $event) {
                echo <<<EVENT
                <div class="event_info">
                    <p class="title">{$event["title"]}</p>
                    <p><span class="date">日付</span>{$event["date"]}</p>
                    <p><span class="place">場所</span>{$event["place"]}</p>
                    <p><span class="category">カテゴリ</span>{$event["category"]}</p>
                    <p><span class="link">リンク</span><a href="{$event["officialsite"]}">公式サイト</a></p>
                </div>
                EVENT;
            }
        }
    }

    class GetPrintEventInfoForSearchResultPHP extends GetPrintEventInfo {
        protected function add_get_event_info($event, $cnt) {
            // 標準化した日付を求める
            $datelist = explode('、',$event["date"]);
            for ($i = 0; $i < count($datelist); $i++) {
                $datelist[$i] = explode("〜", $datelist[$i]);
                $datelist[$i] = ["start"=>$datelist[$i][0], "finish"=>$datelist[$i][1]];
                if ($datelist[$i]["finish"] == "") {
                    $datelist[$i]["finish"] = "none";
                }
                else if (strpos($datelist[$i]["finish"], "年") == FALSE) {
                    $datelist[$i]["finish"] = explode("年", $datelist[$i]["start"])[0] . "年" . $datelist[$i]["finish"];
                }
                preg_match('/[0-9]{4}年[0-9]{1,2}月[0-9]{1,2}/', $datelist[$i]["start"], $datelist[$i]["start"]);
                preg_match('/[0-9]{4}年[0-9]{1,2}月[0-9]{1,2}|none/', $datelist[$i]["finish"], $datelist[$i]["finish"]);
                $datelist[$i]["start"] = str_replace(["年", "月"], "-", $datelist[$i]["start"][0]);
                $datelist[$i]["finish"] = str_replace(["年", "月"], "-", $datelist[$i]["finish"][0]);                        
            }

            // キーワード検索用の文字列を取得
            $united_all_property = " " . $event["title"] . $event["place"] . $event["date"] . $event["category"];

            $this->event_info[$cnt]["normalize_date"] = $datelist;
            $this->event_info[$cnt]["united_all_property"] = $united_all_property;
        }

        private function extraction_by_search_about_category() {
            // カテゴリによる抽出
            $buf_array = [];
            if (isset($_POST["cb"])) {
                for ($i = 0; $i < count($this->event_info); $i++) {
                    $is_match = false;
                    foreach ($_POST["cb"] as $category) {
                        if ($this->event_info[$i]["category"] == $category) {
                            $is_match = true;
                            break;
                        }
                    }
                    if ($is_match) {
                        array_push($buf_array, $this->event_info[$i]);
                    }
                }
            }
            $this->event_info = $buf_array;
        }

        private function extraction_by_search_about_date() {
            // 日付による抽出
            if ($_POST["date"] != "") {
                $buf_array = [];
                for ($i = 0; $i < count($this->event_info); $i++) {
                    $search_date = new DateTime($_POST["date"]);
                    $is_match = false;
                    foreach($this->event_info[$i]["normalize_date"] as $term) {
                        $start_term = new DateTime($term["start"]);
                        if ($term["finish"] == "none") {
                            $finish_term = new DateTime($_POST["date"]);
                        }
                        else {
                            $finish_term = new DateTime($term["finish"]);
                        }

                        if ($start_term <= $search_date and $search_date <= $finish_term) {
                            $is_match = true;
                            break;
                        }
                    }
                    if ($is_match) {
                        array_push($buf_array, $this->event_info[$i]);
                    }
                }
                $this->event_info = $buf_array;
            }
        }

        private function extraction_by_search_about_keyword() {
            // キーワードによる抽出
            if ($_POST["search_keyword"] != "") {
                $separate_word = [".", ",", ":", ";", "|", "\n", "\t", "-", "*", "/", " ", "　", "。", "、", "・"];
                $search_keyword_list = explode("/", str_replace($separate_word, "/", $_POST["search_keyword"]));
                $buf = [];
                foreach ($search_keyword_list as $search_keyword) {
                    if ($search_keyword != "") array_push($buf, $search_keyword);
                }
                $search_keyword_list = $buf;
                $lenght_search_keyword_list = count($search_keyword_list);

                $buf_array = [];
                for ($i = 0; $i < count($this->event_info); $i++) {
                    $match_cnt = 0;
                    foreach ($search_keyword_list as $search_keyword) {
                        if (strpos($this->event_info[$i]["united_all_property"], $search_keyword) != false) {
                            $match_cnt++;
                        }
                    }
                    if ($match_cnt == $lenght_search_keyword_list) {
                        array_push($buf_array, $this->event_info[$i]);
                    }
                }
                $this->event_info = $buf_array;
            }
        }

        function extraction_by_search() {
            $this->extraction_by_search_about_category();
            $this->extraction_by_search_about_date();
            $this->extraction_by_search_about_keyword();

            if ($this->event_info == []) {
                echo "検索に一致するイベントはありません";
                return false;
            }

            return true;
        }
    }

    class GetEventInfoDraeMap extends GetEventInfo {
        public $str_event_info_datas;

        function __construct(){
            parent::__construct();
            $this->str_event_info_datas = "";
        }

        function get_event_info_for_map() {
            foreach($this->event_info as $event) {
                $this->str_event_info_datas .= "/packet=>";
                $this->str_event_info_datas .= "/property=>";
                $this->str_event_info_datas .= $event["title"];
                $this->str_event_info_datas .= "/property=>";
                $this->str_event_info_datas .= $event["officialsite"];
                $this->str_event_info_datas .= "/property=>";

                $placelist=explode('、',$event["place"]);
                foreach( $placelist as $buf ){
                    $this->str_event_info_datas .= "__division__=>";
                    $this->str_event_info_datas .= $buf;
                }
            }
        }
    }
?>