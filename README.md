ALL IMAGE ASSETS OBTAINED FROM https://frutigeraeroarchive.org/


What needs to be added:
- Sort by columns (item id, item name, etc)
- Clicking item rarity on table chooses to filter by that rarity
- Select elements (dropdown menus) are properly styled
- User management page
- Responsiveness
- Error popups for when something goes wrong when doing ANYTHING with the database or just other stuff in general idk :P
- Dashboard with charts

# Items Information System
{description of the project, including where assets are from}

This project was developed for the **Developing Information Systems** module of a foundation degree in **Computer Science and Digital Technologies**.

## Set up
1. Install **XAMPP Control Panel**
2. Start modules **'Apache'** and **'MySQL'**
3. Import the **'php_information_system.sql'** database into **phpMyAdmin** (the version of the SQL being imported may need to be changed to an earlier version than what is currently listed)
4. Create a folder called **'information_system'** in the '**htdocs**' directory of your **'xampp'** directory (e.g. ***C:\\xampp\\htdocs***)
5. Place the **'website'** folder into the newly created **'information_system'** directory
6. Go to **'http://localhost/information_system/website/'** in your browser (renaming any folders will break the website)

All user accounts have *plaintext* passwords stored in the database, allowing you to log in with what you find in the '**users**' table.

---

# Things to note
- The variable names for SQL queries and statements are mixed up throughout this project due to the large period of time between starting and finishing this project with new development practices learnt and implemented over that time. Apologies :P
- There is no password hashing for user accounts as it was not a priority for the outcome of this project.