FROM php:8.1-apache

# Debianのバージョンが古く、パッケージのインデックスファイルの置き場所が変更されてしまっているため
# ビルド時にエラーが出た場合は下記をコメントしてください
RUN echo "deb http://archive.debian.org/debian/ stretch main contrib non-free" > /etc/apt/sources.list && \
    echo "deb-src http://archive.debian.org/debian/ stretch main contrib non-free" >> /etc/apt/sources.list && \
    echo "Acquire::Check-Valid-Until false;" > /etc/apt/apt.conf

# パッケージ追加
RUN apt-get update && apt-get install -y ssl-cert --no-install-recommends && rm -r /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libxml2-dev libzip-dev libpq-dev openssl
RUN apt-get update && apt-get install -y openssl
RUN apt-get update && apt-get install -y vim
RUN apt-get update && apt-get install -y git zip unzip vim
# PHP7.4だとmbstringは廃止のため⇒libonig-dev
RUN apt-get update && apt-get install libonig-dev && docker-php-ext-install pdo_mysql mysqli zip soap

# PDF(wkhtmltopdf)
RUN apt install -y wkhtmltopdf

# Composerのインストール
# RUN:コンテナ内でコマンド実行する
RUN  cd /usr/bin && curl -s http://getcomposer.org/installer | php && ln -s /usr/bin/composer.phar /usr/bin/composer

RUN apt-get install -y zlib1g-dev libpng-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# RUN apt-get update && apt-get install -y libmcrypt-dev \
#     && pecl install mcrypt-1.0.5 \
#     && docker-php-ext-install -j$(nproc) mcrypt

# 日本語
RUN apt-get update \
    && apt-get install -y locales \
    && locale-gen ja_JP.UTF-8 \
    && echo "export LANG=ja_JP.UTF-8" >> ~/.bashrc
RUN apt-get -y install fonts-takao

RUN apt-get update \
    && localedef -i ja_JP -c -f UTF-8 -A /usr/share/locale/locale.alias ja_JP.UTF-8 \
    && update-locale LANG=ja_JP.UTF-8

# ffmpeg
RUN apt-get update \
    && apt-get install -y ffmpeg

# apache
COPY _files/app/web-store.jp_2018.crt /etc/ssl/certs/
COPY _files/app/web-store.jp_2018.chain.crt /etc/ssl/certs/
COPY _files/app/web-store.jp_2018.key /etc/ssl/private/

EXPOSE 80 443
EXPOSE 9000 9001

RUN a2enmod ssl
RUN a2enmod headers
RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2dissite 000-default

COPY _files/app/apache/docker.conf /etc/apache2/sites-available/
RUN a2ensite docker

COPY _files/app/apache/my_httpd.conf /etc/apache2/sites-available/
COPY _files/app/apache/my_mpm_event.conf /etc/apache2/sites-available/
COPY _files/app/apache/my_mpm_prefork.conf /etc/apache2/sites-available/
COPY _files/app/apache/my_mpm_worker.conf /etc/apache2/sites-available/
RUN a2ensite my_httpd
RUN a2ensite my_mpm_event
RUN a2ensite my_mpm_prefork
RUN a2ensite my_mpm_worker

# homeディレクトリ
RUN mkdir -p /home
RUN chown -R www-data: /home
RUN chmod -R 707 /home

# xDebug
RUN pecl channel-update pecl.php.net
RUN pecl install xdebug && docker-php-ext-enable xdebug

# xDebug切り替え用shファイル
COPY _files/app/xdebug_switch.sh /home/tool/xdebug_switch.sh
RUN chmod -R 707 /home/tool/xdebug_switch.sh

# php.ini
COPY _files/app/php/php.ini /usr/local/etc/php/
COPY _files/app/php/php_xdebug_off.ini /home/tool/php_xdebug_off.ini
COPY _files/app/php/php_xdebug_on.ini /home/tool/php_xdebug_on.ini

# PEAR
RUN mkdir -p /home/system/PEAR
COPY _files/app/php/PEAR /home/system/PEAR

#ファイル調整
COPY _files/app/auto_adjustment.sh /home/tool/auto_adjustment.sh
RUN chmod -R 707 /home/tool/auto_adjustment.sh

#work dir
RUN mkdir -p /var/lib/php/session
RUN chmod 777 /var/lib/php/session
RUN mkdir -p /home/tool

#mhsendmail
RUN curl -sSLO https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 \
	&& chmod +x mhsendmail_linux_amd64 \
	&& mv mhsendmail_linux_amd64 /usr/local/bin/mhsendmail

EXPOSE 8025

# editor
COPY _files/app/editor/.vim /root/.vim
COPY _files/app/editor/.vimrc /root/.vimrc
RUN ln -s /root/.vim .vim
RUN ln -s /root/.vimrc .vimrc

# PHP実行とディレクトリのエイリアス作成
RUN ln -s /usr/local/bin/php /usr/bin/php
RUN ln -s /home/local.test.web-store.jp /home/local.test.web-store.jp
RUN ln -s /home/local.trial.web-store.jp /home/local.trial.web-store.jp
RUN ln -s /home/local.laravel.web-store.jp /home/local.laravel.web-store.jp

# ここから固有
# tool
RUN chmod -R 777 /home/tool
RUN chown -R www-data:www-data /home/tool

# log
RUN mkdir -p /home/log
RUN chmod -R 777 /home/log
# apache2/logs
RUN mkdir -p /etc/apache2/logs
RUN chmod -R 777 /etc/apache2/logs
# test
RUN mkdir -p /home/test
RUN chmod -R 777 /home/test
RUN chown -R www-data:www-data /home/test
# trial
RUN mkdir -p /home/trial
RUN chmod -R 777 /home/trial
RUN chown -R www-data:www-data /home/trial
# laravel
RUN mkdir -p /home/laravel
RUN chmod -R 777 /home/laravel
RUN chown -R www-data:www-data /home/laravel

# test
COPY _files/app/apache/local.test.web-store.jp.conf /etc/apache2/sites-available/
RUN a2ensite local.test.web-store.jp

# trial
COPY _files/app/apache/local.trial.web-store.jp.conf /etc/apache2/sites-available/
RUN a2ensite local.trial.web-store.jp

# laravel
COPY _files/app/apache/local.laravel.web-store.jp.conf /etc/apache2/sites-available/
RUN a2ensite local.laravel.web-store.jp

# あまりにマウントファイルが多くなり遅い為、先に展開してから必要分だけマウントとする
# test
COPY source/test /home/test

# trial
COPY source/trial /home/trial

# laravel
COPY source/laravel /home/laravel

# 作業ディレクトリ
WORKDIR /home

# シェル実行
RUN /bin/bash "/home/tool/auto_adjustment.sh"