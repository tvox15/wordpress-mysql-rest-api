# wordpress-mysql-rest-api
A REST API for a custom-implemented wordpress search bar (Completed 1/22/2021
A client asked me to create Add, Edit, Delete, and search bar for his data and implement it on a Wordpress site.

The add, edit, and delete functions are hidden behind a password field (Instead of a session-bassed authentication system)

# Wordpress Pages
There are 4 pages:
Search: Has a search bar, filters, and subfilters that change based on the filter you choose
Results: Displays the results and an edit button. Clicking edit will bring you to the edit page.
Edit entry: If you enter the correct password, you can edit the entry
Add page: If you enter the correct password, you can add an entry

Implementation:
Create 4 wordpress pages with these exact names: add-entry, custom-search, search-results and edit-entries

The main file is called CustomSearch.php. It is uploaded into the classes folder in the wordpress backend and then rendered by adding the following lines wherever you like:

`<addr>` require('wp-content/themes/YOURTHEME/classes/CustomSearch.php');
$customSearch = new CustomSearch();
if($customSearch->onCustomFunctionPage) {} `<addr>`

I'm also sending you a MYSQL file to upload to your database. I added a column for ids, which is necessary. In your website hosting provider backend, look for PHPMyAdmin and open it. Then go into the wordpress database and import the MYSQL file.
If you can't figure out how to implement it of if you have any questions, let me know.
