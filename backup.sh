echo 'comaster2 backup started'
ROOT=$1/comaster2
mkdir $ROOT

# Backup web files
WEBPATH=$ROOT/web
mkdir $WEBPATH
rsync -a . $WEBPATH --exclude .git

# Backup db
DBPATH=$ROOT/db
mkdir $DBPATH
mysqldump -u$2 -p$3 --databases co2db > $DBPATH/dump.sql
echo 'comaster2 backup finished'