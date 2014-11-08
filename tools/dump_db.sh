#!/usr/bin/php
<?
require_once(dirname(__FILE__) . "/../config.php");
$DBNAME = $arr_dbprofiles[DEFAULT_DBPROFILE]['dbname'];
$DB_EXISTS = trim(`echo "SHOW DATABASES;" | mysql -uroot -N | grep -E ^$DBNAME\$`);
if ($DB_EXISTS) {
	`mysqldump -uroot $DBNAME > db.sql`;
} else {
	echo "DataBase $DBNAME does not exist\n";
}
?>
