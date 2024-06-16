#!/bin/bash
### COMMANDS
# db - conect to DB
# <service_name> - join to a container
# <service_name> --sh - join to a container with /bin/sh (default /bin/bash)
#
# <command> --prod - for a production assembly
###

name=com2
args=("$@")

# Check key in arguments
has_key() {
  for arg in "${args[@]}"; do
    if [[ $arg == $1 ]]; then return 0; fi
  done
  return 1
}

# Set prod or dev mode
has_key "--prod" && mode='prod' || mode='dev'

# Set name prefix
prefix="$mode"_"$name"

# Load env variables
source .env

#--- Connect to DB
if has_key "db"; then
  docker exec -it "$prefix"_mysql mysql -u $DB_USER -p$DB_PASS

#--- Init WordPress
elif has_key "init"; then
  docker exec "$prefix"_server php init.php

#--- Join to a container in $1 /bin/sh
elif has_key "--sh"; then
  docker exec -it "$prefix"_$1 /bin/sh

#--- Join to a container in $1
else 
  docker exec -it "$prefix"_$1 /bin/bash
fi