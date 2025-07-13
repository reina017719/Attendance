# 勤怠管理アプリ

## 概要
このアプリはLaravelを用いて作成した勤怠管理システムです。   
社員の出勤・退勤・休憩の記録や、管理者による申請承認、CSV出力機能などを備えています。  
学習目的で作成された模擬案件です。

## 使用技術
- PHP 8.4.2
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1
- Docker
- Fortify

## Dockerビルド
1. git clone git@github.com:reina017719/Attendance.git
2. docker-compose up -d --build

*MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してさい。

## 環境構築
1. docker compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、環境変数を変更
4. php artisan key:generate
5. php artisan migrate
6. php artisan db:seed

## ログイン情報（テスト用）

### 管理者アカウント
- メールアドレス : `admin@coachtech.com`
- パスワード : `adminpass`

### 一般ユーザーアカウント
- メールアドレス : `reina.n@coachtech.com` / `taro.y@coachtech.com`
- バスワード: 全ユーザー共通で`password123`です。

*全一般ユーザー情報は`database/seeders/DatabaseSeeder.php`に記載されてます。

## URL
- 開発環境 : http://localhost/
- phpMyadmin : http://localhost:8080/
- ログイン画面（管理者） : http://localhost/admin/login
- ロブイン画面（一般ユーザー） : http://localhost/login
