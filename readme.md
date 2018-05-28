# Recipe system

The recipe system is a way to digitise your pasted recipes. Just type them 
over into the database, and they will be preserved better and more easily 
searchable.

## Features

- Stores all your recipes in a database
- Allows search by category, ingredients, and vegan/vegetarian
- Lightweight

## Current project status

The project is currently at about Minimum Viable Product: all of the
essential features are there, but there are still some nice to haves that
are missing, such as the ability to edit recipes. Also, a user manual is 
still required, and it's not much of a looker.

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
statements (`false` is recommended in production, for obvious reasons). If
you want the password check to be enforced, also define `$password` as the
sha256 hash of your preffered password.

## User manual

- **Searching:** the search function is not intelligent. It will not
perform a text body search; rather, it just return all recipes that match
you exact search terms
- **Ingredients:** Before an ingredient can be used in a recipe, it must
first be added to the database. This can be done via addingredient.php. You
know an ingredient is in the database if it is suggested by autocomplete; 
*if the ingredient does not show up in autocomplete, the adding of the
recipe will likely fail*. This does not work for ingredients that are
shorter than three characters; for those ingredients, simply try adding
them again.
- **What does "optional" mean?** If you list an ingredient as optional
when adding a recipe, it still shows up in the list of ingredients, with no
indication (although I might change that in a future release). The recipe
will also still show up when searching on that ingredient. However, the 
ingredient will not count when applying filters (vegan or vegetarian). This
means that a pasta that could use grated cheese will still show up when 
searching for vegan recipes.
