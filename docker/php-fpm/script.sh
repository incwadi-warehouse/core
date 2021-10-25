#!/bin/sh

echo "RUN CRONJOB"

/usr/local/apache2/htdocs/bin/console book:delete -q
