// MySQL

作成
CREATE DATABASE データベース名;
CREATE TABLE データベース名 (
    カラム名1 型,  (型の種類: INT(整数型) VARCHAR(文字数)(文字列型) PRIMARY KEY(重複を許さない))
    カラム名2 型,
    ...
)

挿入
INSERT INTO データベース名.テーブル名 (カラム名1, カラム名2...) VALUES (値1, 値2, ...);
取得
SELECT * FROM データベース名.テーブル名;
更新
UPDATE データベース名.テーブル名 SET カラム名1 = 値1,カラム名2 = 値2, ...;
削除
DELETE FROM データベース名.テーブル名;

条件をつけるときは 各コマンドの後に WHERE カラム名 = 値;
というようにする。

<?php
    try {
        $db = new PDO('mysql:host=localhost;dbname=データベース名', 'root', 'root');

        $id = 3;
        $value = "aiueo";

        // SQLクエリ作成
        $stmt = $db->prepare("INSERT INTO テーブル名 VALUES(?, ?);");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->bindParam(2, $value, PDO::PARAM_STR);

        // クエリ実行
        $res = $stmt->execute();

        // 切断
        $db = null;
    }
    catch (PDOException) {
        echo 'データベース接続失敗';
    }
?>