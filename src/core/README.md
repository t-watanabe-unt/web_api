# web_apiのアプリケーション

# 開発環境構築手順

1. 以下のURLより、アプリケーション用のファイルをcloneする
    `https://github.com/t-watanabe-unt/web_api.git`
2. PCのエディター上で、docker-compose.ymlファイルのあるディレクトリへ移動する
   `cd {appディレクトリ}`
3.  `docker-compose build` のコマンドでdockerの開発環境をビルドする
4.  ビルド完了後、`docker-compose up -d` コマンドでdockerを立ち上げる
5.  以下のコマンドを順番にエディター上で入力し実行する

### ▼composerインストール
docker-compose exec web composer install

### ▼権限付与(storage)
docker-compose exec web chown apache:apache -R storage

### ▼シンボリックリンク付与    
docker-compose exec web php artisan storage:link

### ▼ディレクトリ移動
docker-compose exec web mv public /var/www/WWW/public

### ▼DBへテーブルのSQL流し込み(Laravel artisanコマンド)
docker-compose exec web php artisan migrate

# テストコマンド

### テスト開始前
1. .envファイルの`APP_ENV` を`testing` へ変更する
2. 変更後、エディター上で`docker-compose exec web php artisan config:cache` コマンドで`.env` ファイルを再読み込みさせる
3. `docker-compose exec web php artisan env` コマンドで、反映されているかを確認する
   →`The application environment is [testing].  ` と表示されれば、切り替わっている
4. ENVがtestingであれば、以下のテストコマンドを全て実行可能となる
*`APP_ENV` がtestingの時に、`RouteServiceProvider` 内でRateLimitの回数の設定を切り替えている

|区分|テスト内容|コマンド|
|---|---|---|
|全て|全て|php artisan test|
|文書|全体|php artisan test --group=document|
|文書|登録(正常)|php artisan test --group=document_register_pass|
|文書|登録(異常)|php artisan test --group=document_register_error|
|文書|検索(正常)|php artisan test --group=document_search_pass|
|文書|検索(異常)|php artisan test --group=document_search_error|
|文書|削除(正常)|php artisan test --group=document_delete_pass|
|文書|削除(異常)|php artisan test --group=document_delete_error|
|文書の属性|全体|php artisan test --group=document_attributes|
|文書の属性|更新(正常)|php artisan test --group=document_attributes_update_pass|
|文書の属性|更新(異常)|php artisan test --group=document_attributes_update_error|
|文書の属性|削除(正常)|php artisan test --group=document_attributes_delete_pass|
|文書の属性|削除(異常)|php artisan test --group=document_attributes_delete_error|
|文書ファイル|全体|php artisan test --group=document_file|
|文書ファイル|更新(正常)|php artisan test --group=document_file_update_pass|
|文書ファイル|更新(異常)|php artisan test --group=document_file_update_error|
|文書ファイル|ダウンロード(正常)|php artisan test --group=document_file_download_pass|
|文書ファイル|ダウンロード(異常)|php artisan test --group=document_file_download_error|