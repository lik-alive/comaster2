echo 'comaster2 backup started'
ROOT=$1/comaster2
mkdir $ROOT

# Backup web files
WEBPATH=$ROOT/web
mkdir $WEBPATH
rsync -a --info=progress2 . $WEBPATH --exclude .git

# Backup db
DBPATH=$ROOT/db
mkdir $DBPATH
mysqldump -u$2 -p$3 --databases co2db > $DBPATH/dump.sql 2> /dev/null
echo 'comaster2 backup finished'