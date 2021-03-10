#!/bin/bash
host="russel.uberspace.de"
echo $host
echo -n "Enter username: "
read username
ssh -tt $username@$host "\
  cd /var/www/virtual/$username/sites/realtroll.de; \
  git pull; \
  rm -rf storage/cache/realtroll.de; \
"
