#!/bin/sh

# xDebug切り替え処理
if [ "$1" = "on" ]; then
    cp /home/tool/php_xdebug_on.ini /usr/local/etc/php/php.ini
elif [ "$1" = "off" ]; then
    cp /home/tool/php_xdebug_off.ini /usr/local/etc/php/php.ini
elif [ $# -ne 1 ]; then
    echo "引数を設定して実行してください「on または off」"
    exit
else
    echo "引数を正しく設定してください「on または off」"
    echo "例：・・・/xdebug_switch.sh on"
    exit
fi

apachectl graceful

