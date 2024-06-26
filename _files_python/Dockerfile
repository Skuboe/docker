FROM python:3.11.4

ENV TZ=Asia/Tokyo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# libmariadb-dev \
RUN apt update && apt install -y \
    build-essential \
    gcc \
    git \
    ffmpeg \
    && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y ssl-cert --no-install-recommends && rm -r /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libxml2-dev libzip-dev libpq-dev openssl
RUN apt-get update && apt-get install -y openssl
RUN apt-get update && apt-get install -y vim
RUN apt-get update && apt-get install -y git zip unzip vim
RUN apt-get update && apt-get install -y apache2
RUN apt-get update && apt-get install -y npm lsof default-libmysqlclient-dev

# 日本語
RUN apt-get update \
    && apt-get install -y locales \
    && locale-gen ja_JP.UTF-8 \
    && echo "export LANG=ja_JP.UTF-8" >> ~/.bashrc
RUN apt-get -y install fonts-takao

RUN apt-get update \
    && localedef -i ja_JP -c -f UTF-8 -A /usr/share/locale/locale.alias ja_JP.UTF-8 \
    && update-locale LANG=ja_JP.UTF-8
    
EXPOSE 8000 50021

# ffmpeg
RUN apt-get update \
    && apt-get install -y ffmpeg

# apache
COPY _files_python/web-store.jp_2018.crt /etc/ssl/certs/
COPY _files_python/web-store.jp_2018.chain.crt /etc/ssl/certs/
COPY _files_python/web-store.jp_2018.key /etc/ssl/private/

RUN a2enmod ssl
RUN a2enmod headers
RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2dissite 000-default

COPY _files_python/apache/docker.conf /etc/apache2/sites-available/
RUN a2ensite docker

COPY _files_python/apache/my_httpd.conf /etc/apache2/sites-available/
COPY _files_python/apache/my_mpm_event.conf /etc/apache2/sites-available/
COPY _files_python/apache/my_mpm_prefork.conf /etc/apache2/sites-available/
COPY _files_python/apache/my_mpm_worker.conf /etc/apache2/sites-available/
RUN a2ensite my_httpd
RUN a2ensite my_mpm_event
RUN a2ensite my_mpm_prefork
RUN a2ensite my_mpm_worker

# homeディレクトリ
RUN mkdir -p /home
RUN chown -R www-data: /home
RUN chmod -R 707 /home

RUN pip install --upgrade pip
RUN pip3 install "fastapi[all]"
# RUN pip install git+https://github.com/openai/whisper.git
# RUN pip install setuptools
# RUN pip install --upgrade pip setuptools
# RUN pip install -U sentence-transformers
RUN pip install python-dotenv
RUN pip install openai==0.28
RUN pip install mysqlclient
ENV CRYPTOGRAPHY_DONT_BUILD_RUST=1
RUN pip install cryptography==3.4.1
RUN pip install requests
RUN pip install langdetect
RUN pip install gunicorn
RUN pip install pdfminer.six
RUN pip install beautifulsoup4
RUN pip install ffmpeg-python
RUN pip install moviepy
RUN pip install langchain
RUN pip install tiktoken
RUN pip install aiofiles
RUN pip install aiomysql
RUN pip install aiohttp
RUN pip install pandas
RUN pip install lxml
RUN pip install html5lib
RUN pip install bs4

#editor
COPY _files_python/editor/.vim /root/.vim
COPY _files_python/editor/.vimrc /root/.vimrc
RUN ln -s /root/.vim .vim
RUN ln -s /root/.vimrc .vimrc

# PHP実行とディレクトリのエイリアス作成
RUN ln -s /usr/local/bin/php /usr/bin/php
RUN ln -s /home/local.python.docker.jp /home/local.python.docker.jp

# laravel
COPY _files_python/apache/local.python.docker.jp.conf /etc/apache2/sites-available/
RUN a2ensite local.python.docker.jp

# Apache起動
# CMD ["apachectl", "-D", "FOREGROUND"]

WORKDIR /home/python

#ENTRYPOINT ["uvicorn", "main:app", "--host", "0.0.0.0", "--reload"]