# Recipe system

The recipe system is a way to digitise your pasted recipes. Just type them 
over into the database, and they will be preserved better and more easily 
searchable.

## Features

- Stores all your recipes in a database
- Allows search by category, ingredients, and vegan/vegetarian
- Lightweight

## System requirements

- A functional server environment
- Php (tested on php7, php5 should be supported)
- MySql/MariaDB. Porting to other flavours should require minimal effort; 
please fork

## Installation instructions

1. Clone this repository into the desired directory on the server. Make sure 
php has file read permissions for this directory.
2. Run schema.sql on your database
3. Make conn.php, which stores the connection to your database in `$conn`. 
Additionally define a boolean `$debug` to control the printing of debug 
statements (`false` is recommended in production, for obvious reasons) 
