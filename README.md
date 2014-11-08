# A1 PHP Framework

This a PHP5 MVC framework developed in 2006 inspired by Ruby on Rails and CakePHP but with a more light weight nature to be easy to learn and customize. As a framework, it is made up of a library and a group of modules and the developer can add his own modules to it. Some of these modules are users, groups, permissions, translation, pages, configuration.

Each module is made up of 3 parts, 1 or more classes in the model folder. 1 or more classes in the controller folder and one or more folders in the view folder.
The model classes are wrappers to access the entity tables in the database (e.g. page class wraps the pages table). 

Every model class stands for 1 entity table in the database and contains all the operations needed to be done on that table (Follows the ActiveRecord design pattern). 

All model classes inherit parent_model class that is located in lib/ which has the common database access, and data manipulation methods. Model classes are the only places where SQL statements, file acces and data validation are found.

The controller classes are made to collect a group of related use cases (actions) having the same actor. They can also utilize one or more model classes to access the database. A module may have 1 model and 2 controllers if there are 2 different actors with different users groups and permissions accessing the same module from different points of views, like the users module where there is only one model class user.php and 2 controller classes useradmin_controller.php and user_controller.php. The main purpose of the controller methods (actions) is to populate the $__out array to be used by the view.

The view deals with the $__out array received from the controller. It is made up of a folder for each controller containing a tpl file for each viewable action (method in the controller class).

# Framework features:

1- 4 ways to save session data
  - php_session (ordinary way session_start())
  - file_session (saving session data in files in the web application path)
  - db_session (saving session in a database table and keeping track of logged in users session)
  - cookie_session (saving session data for a certain user as a cookie on his PC by encrypting data and writing them to the cookies) https://github.com/amagdy/php_session_in_cookies

2- Single point of link creation and single point of link interpretation. All links are created by the function make_link in (/smarty/libs/plugins/function.make_link.php) and are all read by the function route in /lib/router.php. This helps to change the link and form scheme in the whole web application by changing one or more of these 2 files.

3- global arrays of input and output. The whole system is a black box that takes input in an array named $_\_in and returns output in an array named $_\_out. which makes the system very flexible in the way input and out are provided for that black box.

4- Ability to tweak the same system to use XML web services or AJAX JSON APIs or AMF PHP flash remoting API. This is one of the results of the previous feature.

5- Multi language and Multi-theme support

6- Admin editable permissions. The admin can edit the permissions allowed for a certain group of users even after the launching of the site.

7- Connection to MySQL and SQLite and the ability to add more DB types MySQL and SQLite objects have the same interface (accessible functions) and one of them may be injected in the Parent model class instance at a time to support the required database type.

8- Protection against SQL injection by parent_model methods and convention. Parent model class method provide a clean convention to prepare sql statements and protect against SQL injection.

9- Easy tools for debugging and separating Model and Controller from view. When an error occurs a full stack trace of the error is shown to the developer to find exactly the place of his error. The developer can also show the data in the array of input and output during debug time to see if the problem was in the black box (model and controller) or in the view.

10- Ability to change the path of the web application easily just by editing the HTML_ROOT in config.php this is also one of the results of point (b)

# External Resources
### Smarty 
* http://www.smarty.net/ is the owner of and creator of the smarty template engine located in /smarty/
* But there are 11 files created by me in /smarty/libs/plugins/ and they are:
        block.block_assign.php
        block.border.php
        block.form.php
        block.rte.php
        compiler.client_error_validator.php
        function.date_time_picker.php
        function.error_validator.php
        function.for.php
        function.make_link.php
        function.time_picker.php
        function.times.php

### /lib/service_json.php
* /lib/service_json.php is copyrighted to
* @author Michal Migurski <mike-json@teczno.com>
* @author Matt Knapp <mdknapp[at]gmail[dot]com>
* @author Brett Stimmerman <brettstimmerman[at]gmail[dot]com>
* @copyright 2005 Michal Migurski
* @version CVS: $Id: JSON.php,v 1.30 2006/03/08 16:10:20 migurski Exp $
* @license http://www.opensource.org/licenses/bsd-license.php
* @link http://pear.php.net/pepr/pepr-proposal-show.php?id=198


# Installation
* If you want to change the previous setting edit the HTML_ROOT constant in greetings/config.php
* Install the database file greetings_db.sql to your mysql server that will create a database named greetings_db
* If you want to change the previous setting please change the array $arr_dbprofiles['main'] in greetings/config.php
* Make sure that the apache2_mod_rewrite is installed and enabled and that the site (default site) runs .htaccess files (if this is not possible due to an older version of apache for example, please change the constant value of REWRITE_ENABLED to false in greetings/config.php)
* If you are using Linux or UNIX make sure that the permissions of greetings/log.log are 666 and greetings/smarty//templates_c/ is 777 and that the rest of the files are 644 and the rest of the folders are
755


