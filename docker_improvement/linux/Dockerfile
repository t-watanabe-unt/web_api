# CentOS　使用したいバージョンに合わせて変更
FROM almalinux:8.6

RUN rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-AlmaLinux

# dnf updateしてkernelなどを最新化する
RUN dnf update -y && dnf clean all
RUN dnf -y install glibc-locale-source glibc-langpack-ja

# ロケールと文字コードの設定
RUN localedef -f UTF-8 -i ja_JP ja_JP.UTF-8
ENV LANG="ja_JP.UTF-8" \
    LANGUAGE="ja_JP:ja" \
    LC_ALL="ja_JP.UTF-8"
# タイムゾーンの設定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# httpd、その他必要なモジュールのインストール
RUN dnf -y install dnf-plugin-config-manager httpd zip unzip vim tree git mod_ssl
# httpd系の設定ファイル配置
COPY ./httpd/conf/httpd.conf /etc/httpd/conf/
COPY ./httpd/conf.d/ssl.conf /etc/httpd/conf.d/
COPY ./pki/tls/certs/localhost.crt /etc/pki/tls/certs/
COPY ./pki/tls/private/localhost.key /etc/pki/tls/private/

# yum repository追加
# EPEL
RUN dnf install -y epel-release

# remi
RUN dnf -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm
RUN dnf config-manager --set-enabled remi

# mysql
RUN dnf -y install https://dev.mysql.com/get/mysql80-community-release-el8-4.noarch.rpm
RUN rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022
RUN dnf -y module disable mysql
RUN dnf config-manager --enable mysql80-community

#phpインストール、バージョンに合わせて変更
RUN dnf module install -y php:remi-8.1

# 使用したいPHPのモジュールに合わせて変更
RUN dnf -y install php php-gd php-json php-mbstring php-pdo php-mysqlnd php-pear php-pecl-zip php-xml php-intl php-pecl-mcrypt php-devel php-opcache php-bcmath

# php設定ファイル配置
COPY ./php/php.ini /etc/

#composerインストール
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

#mysqlクライアントインストール
RUN dnf -y install mysql-community-client

# OpenSSH サーバをインストールする
RUN dnf -y install openssh-server

# sshでログインできるようにする
RUN sed -ri 's/^#PermitRootLogin yes/PermitRootLogin yes/' /etc/ssh/sshd_config

# root のパスワードを 設定
RUN echo 'root:root' | chpasswd
# 公開鍵を登録しておかないとログインできない
RUN ssh-keygen -t rsa -N "" -f /etc/ssh/ssh_host_rsa_key

# postfixのインストール
RUN dnf -y install postfix

# sshdの起動設定
RUN systemctl enable sshd

# httpdの起動設定
RUN systemctl enable httpd

# ポートを公開する
EXPOSE 22
EXPOSE 80
EXPOSE 443

# viの文字コード設定変更
#RUN touch ~/.vimrc && echo "set encoding=utf-8" > ~/.vimrc

# initを起動する
CMD ["/sbin/init"]