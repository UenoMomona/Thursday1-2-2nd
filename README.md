# システム開発
*木曜日1,2限*

## 画像投稿掲示板

### 起動方法
`docker compose up`を実行する

### テーブルの作成
各コンテナが起動している状態で、`docker compose exec mysql mysql`で、MySQLクライアントを起動します<br>
以下のSQLを実行し、テーブルを作成します
```sql
CREATE TABLE `bbs_entries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `body` TEXT NOT NULL,
    `image_filename` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    );
```

### ブラウザからのアクセス
以下のURLから掲示板にアクセスすることができます
> http:// { サーバーのアドレス } /bbsimagetest.php
