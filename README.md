# Items Information System In A Frutiger Aero Style
An information system for a database full of random items and statistics with a dashboard, user management and item management. All items were generated using [Mockaroo](https://www.mockaroo.com/).

This project was also a test for a **Frutiger Aero** styled website. All image assets were obtained from **https://frutigeraeroarchive.org/**, and help with CSS for this style came from **https://frutiger-aero.neocities.org/tutorials**.

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

# Tasks that were not completed
- Sort by columns (item id, item name, etc)
- Select elements (dropdown menus) are properly styled
- Responsiveness

# Things to note
These are notes that were taken during development to address certain issues that would/should be addressed in a real development setting.
- It may become apparent that there is a small gimmick present in the project. Logging into the account *'chrisblue'* will change the logout button to be a man's face, who is a tutor for this module and has become a victim of the class' running jokes with replacing his surname with other colours. Another is simply the theme *'Pug'*, which is another running joke of the class.
- The variable names for SQL queries and statements are mixed up throughout this project due to the large period of time between starting and finishing this project with new development practices learnt and implemented over that time. Apologies :P
- There is no password hashing for user accounts as it was not a priority for the outcome of this project.
- A lot of the items have duplicate names due to the limitations of Mockaroo, however this is a demonstration and does not matter.