# OS 使用したいバージョンに合わせて変更
FROM almalinux:8.6

# dnf updateしてkernelなどを最新化する
RUN dnf update -y \
    && dnf -y install glibc-locale-source glibc-langpack-ja \
    && dnf clean all \
    && rm -rf /var/cache/*

# ロケールと文字コードの設定
RUN localedef -f UTF-8 -i ja_JP ja_JP.UTF-8
ENV LANG="ja_JP.UTF-8" \
    LANGUAGE="ja_JP:ja" \
    LC_ALL="ja_JP.UTF-8"

# タイムゾーンの設定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# httpd、その他必要なモジュールのインストール
RUN dnf -y install httpd \
    unzip \
    vim \
    && dnf clean all \
    && rm -rf /var/cache/*

# httpd系の設定ファイル配置
COPY ./httpd/conf/httpd.conf /etc/httpd/conf/
COPY ./httpd/conf.d/ssl.conf /etc/httpd/conf.d/

# EPEL,remi
RUN dnf install -y epel-release \
    && dnf -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm \
    && dnf config-manager --set-enabled remi \
    && dnf clean all \
    && rm -rf /var/cache/*

# php、モジュールのインストール、バージョンに合わせて変更
# 使用したいPHPのモジュール(最低限のものだけ)
RUN dnf module install -y php:remi-8.1 \
    composer \
    && dnf install -y php \
    php-pdo \
    php-mysqlnd \
    && dnf clean all \
    && rm -rf /var/cache/*

# コンテナのプロセスを変更
# Apache MPMをeventからpreforkに変更
RUN sed -i 's/^LoadModule mpm_event_module/#LoadModule mpm_event_module/' /etc/httpd/conf.modules.d/00-mpm.conf \
    && sed -i 's/^#LoadModule mpm_prefork_module/LoadModule mpm_prefork_module/' /etc/httpd/conf.modules.d/00-mpm.conf

# php設定ファイル配置
# php-fpm設定内容修正
COPY ./php/php.ini /etc/
COPY ./php/www.conf /etc/php-fpm.d/www.conf

# httpdの起動設定
# Apacheのログを標準出力に出力する設定
RUN dnf -y install systemctl enable httpd \
    && dnf clean all \
    && rm -rf /var/cache/*

# ポートを公開
EXPOSE 80
EXPOSE 443

WORKDIR /var/www/core

# 緩やかな停止(graceful-stop)
STOPSIGNAL SIGWINCH

# 1プロセスで起動するように修正
CMD ["httpd", "-DFOREGROUND"]