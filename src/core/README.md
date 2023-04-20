# web_api

# テストコマンド

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