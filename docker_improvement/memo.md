■現状のファイルの改善点は？
・RUNコマンドが多すぎるのでまずはまとめる(分けすぎ)
    →RUNコマンドを分けて記載して、キャッシュがある場合に変更した内容が反映されない場合がある

・そのままdocker-compose upのコマンドを打つだけでは使えない(環境が完全に構築できない)
i)使えない理由は
    ・DBは構成されているが、テーブル情報が全くない状態
    ・vendorがない
    ・ディレクトリのシンボリックリンクが張られていない
    ・ディレクトリの権限が設定されていない

ii)改善ポイント
    ・Dockerfile?で、テーブルも作成するようにコマンドを準備
    ・Dockerfileで、vendorもインストールするコマンドを準備
    ・Dockerfileで、シンボリックリンクを張るコマンドを準備

・汎用性が全くない

・dockerhubの機能を全く使っていない

■ポイント
・ aptの場合、キャッシュを削除することで、
    さらに、apt キャッシュを削除してクリーンアップすると、
    /var/lib/apt/listsapt キャッシュはレイヤーに保存されないため、imageサイズが小さくなります。
    RUNステートメントは で始まるため apt-get update、パッケージ キャッシュは常に の前に更新されますapt-get install。
    公式の Debian および Ubuntu イメージは自動的に実行されるapt-get cleanため、明示的な呼び出しは必要ありません。
・ADDを使用してリモートURLからパッケージを取得することはだめ。代わりに、curlやwgetを使用し取得すること
・`latest` タグに依存しないこと
・データ保存は、volumeを使用してデータを保存する(imageサイズが大きくなる)

セキュリティ面で
・各コンテナーは、1 つの責任のみを持つ必要があります。
・コンテナーは不変で、軽量で、高速でなければなりません。
・コンテナにデータを保存しないでください。代わりに共有データ ストアを使用してください。
・コンテナーは、簡単に破棄して再構築できる必要があります。
・小さなベース イメージ (Linux Alpine など) を使用します。画像が小さいほど、配布しやすくなります。
・不要なパッケージのインストールは避けてください。これにより、イメージがクリーンで安全に保たれます。
・ビルド時にキャッシュ ヒットを回避します。
・デプロイ前にイメージを自動スキャンして、脆弱なコンテナーを本番環境にプッシュしないようにします。
・脆弱性について、開発中と実稼働中の両方でイメージを毎日分析します。それに基づいて、必要に応じてイメージの再構築を自動化します
→イメージを開発するときは、このページで概説されているベスト プラクティスに従うだけでなく、
    脆弱性検出ツールを使用してイメージのセキュリティ体制を継続的に分析および評価することも重要です。

▼docker-composeファイル書き方
・services: でアプリケーションを定義


参考ページ)
    ▼開発のベストプラクティス(imageを小さくする方法など)
    https://docs.docker.com/develop/dev-best-practices/

    ▼Dockerfileのベストプラクティス
    https://docs.docker.com/develop/develop-images/dockerfile_best-practices/

    ▼ADDコマンド
    https://docs.docker.com/engine/reference/builder/#add

    ▼COPYコマンド
    https://docs.docker.com/engine/reference/builder/#copy

    ▼セキュリティのベストプラクティス
    https://docs.docker.com/develop/security-best-practices/


@memo
・Play with Docker は amd64 プラットフォームを使用します。
    Apple Silicon で ARM ベースの Mac を使用している場合は、
    イメージを再構築して Play with Docker と互換性を持たせ、
    新しいイメージをリポジトリにプッシュする必要があります。
    amd64 プラットフォーム用のイメージをビルドするには、--platformフラグを使用します。
・コンテナが起動するためには、Dockerfileが必要
・各コンテナは、分離されている
・volumesを作成することで、コンテナ間で保存されたデータを共有可能になる
・volumeを作成した場合、SQLiteデータベースとして保存される
・データベースのコンテナにvolumesの保存ディレクトリを指定することで、どこに保存(アプリケーション)されているのかを指定できる
・docker volume inspect `volumes名`のコマンドで、どのデータベースでマウントされているかを確認できる 
・MySQL接続時の情報を以下のように設定ファイルとして読み込むことができる
たとえば、MYSQL_PASSWORD_FILEvar を設定すると、アプリは参照ファイルの内容を接続パスワードとして使用します。
Docker は、これらの環境変数をサポートするために何もしません。
アプリは、変数を探してファイルの内容を取得することを知る必要があります。

Dockerfile内で、ENVを指定し(変数)、その変数を切り替えて読み込む内容を修正できるように
ADD
COPY
ENV
EXPOSE
FROM
LABEL
STOPSIGNAL
USER
VOLUME
WORKDIR
ONBUILD