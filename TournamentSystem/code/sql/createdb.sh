#!/bin/sh

DB=$1 ;

stty -echo
echo -n 'password: ' ;
read pw ;
stty echo

mysql -u root --password=$pw <<SQL
drop database if exists $DB  ;
create database if not exists $DB ;
use $DB ;
SQL

mysql $DB -u root --password=$pw < $2
mysql $DB -u root --password=$pw < populateGameTypes.sql
mysql $DB -u root --password=$pw < populateLocations.sql
