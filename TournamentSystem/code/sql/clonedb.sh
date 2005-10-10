#!/bin/sh

DB_FROM=$1 ;
DB_TO=$2 ;

if [ $DB_TO == 'qwus' ]; then
    echo 'qwus cannot be target db' ;
    exit ;
fi;

stty -echo
echo -n 'password: ' ;
read pw ;
stty echo

echo ;

echo -n 'About to remove database: '$DB_TO'.  Press any key to continue.' ;
read answer ;

mysql -u root --password=$pw <<SQL
drop database if exists $DB_TO  ;
create database if not exists $DB_TO ;
use $DB_TO ;
SQL

mysqldump $DB_FROM -u root --password=$pw | mysql $DB_TO -u root --password=$pw

echo ;
