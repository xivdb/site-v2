#!/bin/bash
rm -rf database_update.sql
mysql xivdb -uxivdb -pxivdb -N -e 'show tables like "xiv\_%"' | xargs mysqldump xivdb -uxivdb -pxivdb > database_update.sql
