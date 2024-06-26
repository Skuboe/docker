FROM mysql:8.0

EXPOSE 3306

COPY _files/db/my.cnf /etc/mysql/conf.d
COPY _files/db/0_user.sql /docker-entrypoint-initdb.d/0_user.sql
COPY _files/db/0_create_database.sql /docker-entrypoint-initdb.d/0_create_database.sql

COPY _files/db/data/* /tmp/