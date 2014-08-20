make_db.php is the script which produces bloodaxe_db.json. It depends on: 

1. 'read_archive.php' which parses the file 'edit.xml'. this latter is the export of archiveshub catalgoue.
2. first you must produce 'portfolio.json' if this does not already exist. This is the info around book covers from portfolio server. This file is produced usign 'get_book_db.php'

Finally you can call 'make_db.php' to create the database json file. If you want to read it for debugging purposes there's a 'read_db.php' script in there too which you can fiddle with.