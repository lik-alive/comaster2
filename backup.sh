title=comaster2

echo $title backup started
ROOT=$1/$title
mkdir $ROOT

# Backup web files
WEBPATH=$ROOT/web
mkdir $WEBPATH
#rsync -a --info=progress2 . $WEBPATH --exclude .git
tar cf - --exclude ".git" . | pv -s $(du -sb . | awk '{print $1}') | gzip > $WEBPATH/$title.tar.gz

# Backup db
DBPATH=$ROOT/db
mkdir $DBPATH
mysqldump -u$2 -p$3 --databases co2db > $DBPATH/dump.sql 2> /dev/null

echo $title backup finished
