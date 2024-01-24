# システム開発
*木曜日1,2限 後期課題*

## 画像投稿掲示板

### 起動方法
`docker compose up`を実行する

### テーブルの作成
各コンテナが起動している状態で、`docker compose exec mysql mysql`で、MySQLクライアントを起動します<br>
以下のSQLを実行し、テーブルを作成します
```sql
CREATE TABLE `users` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` TEXT NOT NULL,
	`email` TEXT NOT NULL,
	`password` TEXT NOT NULL,
	`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`icon_filename` TEXT DEFAULT NULL,
	`self_introduction` TEXT DEFAULT NULL,
	`cover_filename` TEXT DEFAULT NULL,
	`birthday` date DEFAULT NULL
);

CREATE TABLE `user_relationships` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`followee_id` INT NOT NULL,
	`follower_id` INT NOT NULL,
	`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `exam_bbs_entries` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` INT NOT NULL,
	`body` TEXT NOT NULL,
	`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `exam_bbs_images` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` INT NOT NULL,
	`entry_id` INT NOT NULL,
	`image_filename` TEXT NOT NULL
);

```

### ブラウザからのアクセス
以下のURLから掲示板にアクセスすることができます
> http:// { サーバーのアドレス } /timeline.php
