Files in this directory set up mysql tables, views, stored procedures, and
functions used by the FeedApp. The majority of these files contain a single
table/view/etc... definition and are relatively straightforward. The
documentation below describes the functionality of non-straightforward files.


## create_mysql_installer.php
Creates a SQL script (install_mysql.sql) with all the required SQL statements
to create the database index.

## install_mysql.sql
This is a dynamically generated script based on the other scripts in this
directory structure. As such, this file is not part of the versioned content but
can be quickly created by running the create_mysql_installer.php script.


## fdsnws
Contains scripts to augment the base index with behavior in support of the FDSN
web service.

### indexes.sql
Augments tables from the indexer package with additional index to imporove
query performance for the web service. Note that indexes managed by this file
are first deleted if they previously existed and then recreated.

### summary_sql.php
Dynamically creates the statements for summarizing individual products. This
script is dynamically executed by the create_mysql_installer.php when necessary
and the output is placed directly into install_mysql.sql.


## feplus
Contains scripts to augment the base index with behavior in support of FE+
functionality.

### data.sql
Contains all the data for the FE+ table. Data is initially inserted as text and
converted to spatial data afterwards. Finally, indexes are added for
performance.


## indexer
Contains scripts to create the base index schema.