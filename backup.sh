#!/bin/bash
### INFO
# sudo ./backup.sh <dest_folder>
###

title=comaster2

echo $title backup started
ROOT=$1/$title
mkdir $ROOT

# Backup source files
tar cf - --exclude ".git" --exclude "wp-admin" --exclude "wp-includes" --exclude "index.php" . | pv -s $(du -sb . | awk '{print $1}') | gzip > $ROOT/$title.tar.gz

echo $title backup finished