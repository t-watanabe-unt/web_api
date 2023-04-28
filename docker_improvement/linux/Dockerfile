# OS 使用したいバージョンに合わせて変更
FROM almalinux:8.6

# dnf updateしてkernelなどを最新化する
RUN rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-AlmaLinux \
    && dnf update -y \
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
RUN dnf -y install dnf-plugin-config-manager \
    httpd \
    zip \
    unzip \
    vim \
    tree \
    git \
    mod_ssl \
    && dnf clean all \
    && rm -rf /var/cache/*

# httpd系の設定ファイル配置
COPY ./httpd/conf/httpd.conf /etc/httpd/conf/
COPY ./httpd/conf.d/ssl.conf /etc/httpd/conf.d/
COPY ./pki/tls/certs/localhost.crt /etc/pki/tls/certs/
COPY ./pki/tls/private/localhost.key /etc/pki/tls/private/

# EPEL
# remi
# mysql
RUN dnf install -y epel-release \
    && dnf -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm \
    && dnf config-manager --set-enabled remi \
    && dnf -y install https://dev.mysql.com/get/mysql80-community-release-el8-4.noarch.rpm \
    && rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022 \
    && dnf -y module disable mysql \
    && dnf config-manager --enable mysql80-community \
    && dnf clean all \
    && rm -rf /var/cache/*

# php、モジュールのインストール、バージョンに合わせて変更
# 使用したいPHPのモジュール(最低限のものだけ)
RUN dnf module install -y php:remi-8.1 \
    && dnf install -y php \
    php-pdo \
    php-mysqlnd \
    && dnf clean all \
    && rm -rf /var/cache/*

# php設定ファイル配置
# php-fpm設定内容修正
COPY ./php/php.ini /etc/
COPY ./php/www.conf /etc/php-fpm.d/www.conf

# composerインストール
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer \
    && dnf clean all \
    && rm -rf /var/cache/*

# mysqlクライアントインストール
# postfixのインストール
# httpdの起動設定
RUN dnf -y install mysql-community-client \
    && systemctl enable httpd \
    && dnf clean all \
    && rm -rf /var/cache/*

# ポートを公開
EXPOSE 80
EXPOSE 443

WORKDIR /var/www/core

# initを起動する
CMD ["/sbin/init"]