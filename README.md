# wordpress-mysql-rest-api (PHP, MySQL, HTML, CSS, Javascript, Jquery)
A REST API for a custom-implemented wordpress search bar (Completed 1/22/2021)

A client asked me to create Add, Edit, Delete, and search bar for his data and implement it on a Wordpress site.

The add, edit, and delete functions are hidden behind a password field (Instead of a session-bassed authentication system).

The search bar is open for anyone to use. I have added images in the images folder for easy viewing of each page.

# CSS
It is 100% custom CSS. Jquery is used for changing the filters and subfilters and displaying error messages

# Security
I use PDO prepared statements 

# Wordpress Pages
There are 4 pages:

Search: Has a search bar, filters, and subfilters that change based on the filter you choose

Results: Displays the results and an edit button. Clicking edit will bring you to the edit page.

Edit entry: If you enter the correct password, you can edit the entry

Add page: If you enter the correct password, you can add an entry

# Implementation:
Create 4 wordpress pages with these exact names: add-entry, custom-search, search-results and edit-entries

The main file is called CustomSearch.php. It is uploaded into the classes folder in the wordpress backend and then rendered by adding the following lines wherever desired:

```
require('wp-content/themes/YOURTHEME/classes/CustomSearch.php');
$customSearch = new CustomSearch();
if($customSearch->onCustomFunctionPage) {} 
```

