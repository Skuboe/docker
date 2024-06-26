# 更新履歴
<details>
<summary>
更新履歴の表示
</summary>

|  日時  |  更新内容  |
| ---- | ---- |
|  2023/08/08  |  新プロジェクト用Docker環境構築  |

</details>

# 目次
<details>
<summary>
目次の表示
</summary>

0 [docker準備](#anchor1)<br>
- [Dockerダウンロード](#anchor0_1)<br>
- [Dockerインストール](#anchor0_2)<br>
- [WSL2のインストール](#anchor0_3)<br>
- [WSL2設定](#anchor0_4)<br>
- [Proxy利用の場合](#anchor0_5)<br>

1 [ソースコードの配置](#anchor1)<br>
- [Gitの設定変更](#anchor1_1)<br>
- [ソースコードの clone](#anchor1_2)<br>
- [shファイル](#anchor1_3)<br>

2 [設定](#anchor2)<br>
- [hosts設定](#anchor2_1)<br>
- [開発環境](#anchor2_2)<br>
  - [新規プロジェクト](#anchor2_2_1)<br>
- [xDebug設定](#anchor2_3)<br>
- [ymlファイル設定](#anchor2_4)<br>
- [同期設定](#anchor2_5)<br>
  - [通常](#anchor2_5_1)<br>
  - [UNISON](#anchor2_5_2)<br>

3 [起動・停止](#anchor3)<br>
- [ビルド](#anchor3_1)<br>
- [起動](#anchor3_2)<br>
- [起動確認](#anchor3_3)<br>
- [停止](#anchor3_4)<br>

4 [操作関連](#anchor4)<br>
- [サーバアクセス](#anchor4_1)<br>
- [編集反映設定(マウント)変更](#anchor4_2)<br>
- [xDebugの有効・無効](#anchor4_3)<br>
- [ファイル処理](#anchor4_4)<br>

5 [その他](#anchor5)<br>
- [DockerのGit管理](#anchor5_1)<br>
- [Docker Hub](#anchor5_2)<br>

6 [MailHog](#anchor6)<br>
- [ブラウザアクセス](#anchor6_1)<br>

7 [UNISON](#anchor7)<br>
- [上級編（Windows）（お勧め度：★★★★☆）](#anchor7_1)<br>

</details>

# 0. <a id="anchor0"></a>docker準備

## 0.1 <a id="anchor0_1"></a>Dockerダウンロード
[Docker Desktop](https://www.docker.com/products/docker-desktop) をインストール

## 0.2 <a id="anchor0_2"></a>Dockerインストール

WindowsユーザはWSL2は有効にする

※Windows Proとかを使っていてHyper-Vを利用できる場合はそちらを使ったほうが早いです

## 0.3 <a id="anchor0_3"></a>WSL2のインストール「Windowsユーザのみ」

０１：管理者権限でPowerShellを実行

０２：Linux 用 Windows サブシステムを有効にする

```shell
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
```

０３：再起動

０４：Linux カーネル更新プログラム パッケージをダウンロードする

　　[Linux カーネル更新プログラム](https://wslstorestorage.blob.core.windows.net/wslblob/wsl_update_x64.msi)

０５：「wsl --install」と入力

０６：インストールしたいディストリビューションを選ぶ

　　※例「wsl --install -d Ubuntu」

０７：ディストリビューションをインストールする

０８：「wsl --list --verbose」を実行する

０９：「VERSION」が「2」になっていることを確認する

　　「2」でなければ「wsl --set-version Ubuntu 2」として「2」に変更する

１０：Dockerでディストリビューションを設定する

　　設定画面にある「Resources＞WSL INTEGRATION」を選択して、インストールしたディストリビューションをONにする

１１：エクスプローラ等で「\\wsl$」としてアクセスできれば完了

## 0.4 <a id="anchor0_4"></a>WSL2設定「Windowsユーザのみ」

WSLを利用すると、メモリが枯渇する不具合が存在する

参考：https://qiita.com/yoichiwo7/items/e3e13b6fe2f32c4c6120

こちらを解消するため、メモリの制限を追加する

「%USERPROFILE%\」配下に、「.wslconfig」を設置または変更する

```shell
[wsl2]
memory=6GB
swap=0
```

・memory

WSL2が最大確保するメモリサイズを指定します。PCの搭載メモリとWSL2の使用用途に応じてメモリサイズの値は変更してください。ちなみに未指定時のデフォルト値はPC搭載メモリの50%または8GBのうち、少ない方の値です。(WSL2バージョンがBuild 20175未満の場合はPC搭載メモリの80%)

・swap

0に設定してスワップを無効にします。
スワップを無効にするとWSL2側で実際にメモリ不足になった途端に変な挙動を起こすかもしれません。しかしその代わりSSD等で大量のスワップIn/Outの発生を防ぐため、大量書き込みによるSSDの寿命を縮めることは回避できます。トレードオフを意識して設定するかどうかを考えましょう。

## 0.5 <a id="anchor0_5"></a>Proxy利用の場合

プロキシを設定してアクセスしている場合

Dockerへプロキシ設定が必要。

■Setting > Resources > PROXIES

```shell
[Manual proxy configuration]
チェックON

[プロキシ設定]
Web Server(HTTP)：http://ユーザ:パスワード@プロキシ:ポート
Secure Web Server(HTTPS)：http://ユーザ:パスワード@プロキシ:ポート
```

■Windowsの設定

プロキシの設定を変更する > プロキシ

下記箇所に設定を追加する

「次のエントリで始まるアドレス以外にプロキシ サーバーを使います。エントリを区切るにはセミコロン(;)を使います。」

```shell
・・・;local.test.web-store.jp;local.trial.*;local.laravel.*;
```

■ymlファイルの設定

環境変数を利用していますので利用するymlファイルの#（コメント）を解除してください。

「PROXY_ID」「PROXY_PASS」にはプロキシで利用するIDとパスワードを入力してください。

```shell
    environment:
      TZ: Asia/Tokyo
      LANG: ja_JP.UTF-8
      LANGUAGE: ja_JP.UTF-8
      LC_ALL: ja_JP.UTF-8
      #PROXY_HOST: XX.XXX.XXX.XX
      #PROXY_PORT: XXXX
      #PROXY_ID: ユーザ名を記載してください
      #PROXY_PASS: パスワードを記載してください
```

# 1. <a id="anchor1"></a>ソースコードの配置

## 1.1 <a id="anchor1_1"></a>Gitの設定変更「Windowsユーザのみ」

WIndowsの場合のみ、GitにてCRLFやLF変換をしてしまう場合があるので設定を変更する。

０１：git config --global core.autocrlf false

※参考サイト：「https://prograshi.com/platform/docker/convert-crlf-to-lf/」

## 1.2 <a id="anchor1_2"></a>ソースコードの clone

dockerは任意の場所にクローンしてください。

※Dockerの「_files」配下にクローンする

```shell
mkdir docker
cd docker

# docker
git clone https://github.com/Skuboe/docker.git docker
```

<details>
<summary>
docker構成
</summary>

```shell
# docker構成
docker
 |-- _files/
 |      |-- app
 |      |      |-- apache
 |      |      |      |-- docker.conf
 |      |      |      |-- local.test.web-store.jp.conf
 |      |      |      |-- local.trial.web-store.jp.conf
 |      |      |      |-- local.laravel.web-store.jp.conf
 |      |      |      |-- my_httpd.conf
 |      |      |      |-- my_mpm_event.conf
 |      |      |      |-- my_mpm_prefork.conf
 |      |      |      └-- my_mpm_worker.conf
 |      |      |-- editor
 |      |      |      |-- .vim
 |      |      |      └-- .vimrc
 |      |      |-- mailhog
 |      |      |      └-- mail.ini
 |      |      |-- php
 |      |      |      |-- PEAR
 |      |      |      |-- php.ini
 |      |      |      |-- php_xdebug_off.ini
 |      |      |      └-- php_xdebug_on.ini
 |      |      |-- auto_adjustment.sh
 |      |      |-- Dockerfile
 |      |      |-- ※web-store.jp_2018.chain.crt
 |      |      |-- ※web-store.jp_2018.crt
 |      |      |-- ※web-store.jp_2018.key
 |      |      └-- xdebug_switch.sh
 |      └-- db
 |             |-- data
 |             |      └-- empty.txt
 |             |-- ※persistence
 |             |      └-- ・・・（DB永続化保持場所）
 |             |-- 0_create_database.sql
 |             |-- 0_user.sql
 |             |-- Dockerfile
 |             └-- my.cnf
 |-- _files_python/
 |      └-- app
 |             |-- apache
 |             |      |-- docker.conf
 |             |      |-- local.python.web-store.jp.conf
 |             |      |-- my_httpd.conf
 |             |      |-- my_mpm_event.conf
 |             |      |-- my_mpm_prefork.conf
 |             |      └-- my_mpm_worker.conf
 |             |-- editor
 |             |      |-- .vim
 |             |      └-- .vimrc
 |             |-- Dockerfile
 |             |-- ※web-store.jp_2018.chain.crt
 |             |-- ※web-store.jp_2018.crt
 |             └-- ※web-store.jp_2018.key
 |-- source/
 |      |-- ※test
 |      |-- ※trial
 |      └-- ※laravel
 |-- sync-docker-compose.yml
 |-- README.md
 |-- sync-unison-win.bat
 └-- unison-docker-compose.yml
 「※」マークはクローンしても含まれていません。
```
</details>

## 1.3 <a id="anchor1_3"></a>shファイル

下記記載のファイルをエディタなどで開き改行コードをLFに変更してください。

必要なものは、LFにしてプッシュしていますがおかしいエラーが出た場合は変更してください。

さくらエディタであれば、「置換」で「正規表現」にチェックをいれて

「\r\n」を「\n」に置換等

<details>
<summary>
LF対象ファイル
</summary>

```shell
docker
  |-- _files/
  |     |-- app
  |     |      |-- apache
  |     |      |      |-- docker.conf
  |     |      |      |-- local.test.web-store.jp.conf
  |     |      |      |-- local.trial.web-store.jp.conf
  |     |      |      |-- local.laravel.web-store.jp.conf
  |     |      |      |-- my_httpd.conf
  |     |      |      |-- my_mpm_event.conf
  |     |      |      |-- my_mpm_prefork.conf
  |     |      |      └-- my_mpm_worker.conf
  |     |      |-- editor
  |     |      |      |-- .vim
  |     |      |      └-- .vimrc
  |     |      |-- mailhog
  |     |      |      └-- mail.ini
  |     |      |-- php
  |     |      |      |-- php.ini
  |     |      |      |-- php_xdebug_off.ini
  |     |      |      └-- php_xdebug_on.ini
  |     |      |-- auto_adjustment.sh
  |     |      └-- xdebug_switch.sh
  |     └-- db
  |            └-- my.cnf
  └-- _files_python/
        └-- app
               |-- apache
               |      |-- docker.conf
               |      |-- local.python.web-store.jp.conf
               |      |-- my_httpd.conf
               |      |-- my_mpm_event.conf
               |      |-- my_mpm_prefork.conf
               |      └-- my_mpm_worker.conf
               └-- editor
                      |-- .vim
                      └-- .vimrc
```

</details>

# 2. <a id="anchor2"></a>設定

## 2.1 <a id="anchor2_1"></a>hosts設定

```shell
127.0.0.1 local.test.web-store.jp
127.0.0.1 local.trial.web-store.jp
127.0.0.1 local.laravel.web-store.jp
127.0.0.1 localhost
```

## 2.2 <a id="anchor2_2"></a>開発環境

### 2.2.1 <a id="anchor2_2_1"></a>新規プロジェクト

source配下の「laraveli」にクローンする

## 2.3 <a id="anchor2_3"></a>xDebug設定

_files/app配下のphp.iniの編集を行う

※デフォルトはxDebugは無効になっています

※下記設定を参考

_files/app配下のphp_xdebug_off.iniの編集を行う

※特に変更する必要はありません

_files/app配下のphp_xdebug_on.iniの編集を行う

※下記設定値を参考

設定箇所は下記となります

※デフォルトのポート設定は9001のため自身の環境に合わせてください

※Dockerの設定としては「9000」と「9001」のみがデフォルトで利用可能となっています

※末尾に「xdebug.remote_port=9001」とありますのでこの数値を変更してください

```shell
[xdebug]
zend_extension=xdebug
xdebug.mode=debug
xdebug.client_host=host.docker.internal
xdebug.client_port=9001
xdebug.start_with_request=yes
xdebug.idekey = ide-xdebug
```

## 2.4 <a id="anchor2_4"></a>ymlファイル設定

自身のディレクト構成にあわせて書き換えしてください

利用しないディレクトリやサブドメインがいらない場合は、マウント箇所をコメントアウト「#」してください

※COPY処理がはいっているので不要であればダミーをいれてください

書き換えるファイルは下記となります

```shell
sync-docker-compose.yml
unison-docker-compose.yml
```

### 2.5 <a id="anchor2_5"></a>同期設定

### 2.5.1 <a id="anchor2_5_1"></a>通常

下記部分を自身の環境にあわせて書き換えてください

※相対パスで指定してください

```shell
# windows
d:/source/

# Mackintosh
/Users/{user_name}}/{XXXX}/home/

# 相対パスの例題
./source/
```

### 2.5.2 <a id="anchor2_5_2"></a>UNISON

現状はディレクトリ全てを同期させます。

細かく設定したいのであれば下記ファイルを編集してください。

```shell
sync-unison-win.bat
```

# 3. <a id="anchor3"></a>起動・停止

## 3.1 <a id="anchor3_1"></a>ビルド

_dockerディレクトリにて

```shell
docker-compose -f sync-docker-compose.yml build
```

※Volumeでマウントしていないファイルの更新が必要な場合、ビルドが必要。  
　DBも初期化されるため、必要に応じデータをバックアップしておく  
※DB構造の更新だけならSQL流してしまうという選択肢もある。

## 3.2 <a id="anchor3_2"></a>起動

_dockerディレクトリにて

初回起動の方でVagrant環境があるかた

　Dockerを起動する前にVagrantで利用していたデータベースのダンプをとっておいてください

```shell
# 通常
docker-compose -f sync-docker-compose.yml up -d

# unison同期
docker-compose -f unison-docker-compose.yml.yml up -d

# --build をつけると再ビルドして起動できる
# 初回起動は、buildはつけてください
# buildを単発で実行した後であれば初回起動時にbuild付与は不要です
docker-compose -f ****.yml up -d --build {サービス...}
```

ビルド時にエラーがおきた場合は、下記ファイルの行頭のコメントを解除してお試しください

```shell
_files/app/Dockerfile

# RUN echo "deb http://deb.debian.org/debian jessie main" > /etc/apt/sources.list && echo "deb http://security.debian.org jessie/updates main" >> /etc/apt/sources.list
```

## 3.3 <a id="anchor3_3"></a>起動確認

大体DBの起動待ち
```shell
docker logs -f {指定のdbコンテナ}
```

## 3.4 <a id="anchor3_4"></a>停止

_dockerディレクトリにて

```shell
docker-compose -f ****.yml down
```

Volume削除する場合は -v をつける

# 4. <a id="anchor4"></a>操作関連

## 4.1 <a id="anchor4_1"></a>サーバアクセス

```shell
docker exec -it {コンテナ名} bash
```

としてサーバにログイン出来る

## 4.2 <a id="anchor4_2"></a>編集反映設定(マウント)変更

buildした時とマウント外ファイル差分が存在しない前提  
(その場合、buildから必要。動かなかったらでも良いと思う)

* 停止
* 編集反映：反映させたいpathがymlのvolumeマウントされるよう編集
* 起動

## 4.3 <a id="anchor4_3"></a>xDebugの有効・無効

初期時では、xDebugは無効となっております
有効にした場合、3秒～5秒くらいの処理時間がかってしまいますので
必要なときに、ONにすることをお勧めします

```shell
# xDebug有効
docker exec -it {コンテナ名} /home/tool/xdebug_switch.sh on

# xDebug無効
docker exec -it {コンテナ名} /home/tool/xdebug_switch.sh off
```
## 4.4 <a id="anchor4_4"></a>ファイル処理

ファイル等を書き換えて自動で同期される場合改行コードや文字コードが変わってしまい
正常に同期ができなく、画面上NOTICEやWARNIGが表示される場合がある
その場合、同期した設定ファイルを一括で正常に修正してくれるシェルスクリプトとなります
※docker内に入りvimで指定のファイルを開いてwqで保存する処理です
　ついでに、パーミッションが権限がなくなる場合がありますので777に設定する処理を含めています。

```shell
# ファイル処理
docker exec -it {コンテナ名} bash /home/tool/auto_adjustment.sh
```

# 5. <a id="anchor5"></a>その他

## 5.1 <a id="anchor5_1"></a>DockerのGit管理

Git管理にないファイルが大量に発生しますので下記「.gitignore」配置をお勧めします

```shell
.gitignore
```
## 5.2 <a id="anchor5_2"></a>Docker Hub

Dockerは有料となっております。（条件に満たす場合のみ）

上記に伴って、DockerHubも有料となりゲストであればIP判断によって6時間で100回までのプルが可能

無料会員であれば、アカウントによって6時間で200回までのプルが可能となっております。

VPNをつなぐと皆同じIPとなり、練習などすると100回を超えてしまう恐れがありますので

DockerHubの無料会員となり、Dockerを実行する。

# 6. <a id="anchor6"></a>MailHog

## 6.1 <a id="anchor6_1"></a>ブラウザアクセス

下記でアクセスすることで、MailHogにアクセスすることが可能となります

※永続化はしていません

```shell
http:localhost:8025
※httpsではりません
```