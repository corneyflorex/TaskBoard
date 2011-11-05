About
======
A task board, enabling anons around the world to:

* Post small tasks, ex. "Make poster for #SomeOp"
* Reply to tasks with a message or file upload
* Bump tasks when they receive participants
* Search for tasks by language, country, tags
* Delete tasks using a password or keyfile
* Use all functionality without registering (with captcha)

Tasks:

* Begin life at the top of the Recent Tasks board
* Drift downward as other tasks are created or bumped
* Move to the Active Tasks board while receiving heavy participation
* Expire after 24 hours without activity

Stuff that still needs to be coded:
* Moderation/Admin
* Voting
* Spam filter
* Stickied message per tag ( goes to the top of the page on particular tags)

Requirments
======

* PHP
* SQLite

Install (old)
======

Simply place the contents of this repository in a suitable place on your webserver.

If necessary, initialize the database by navigating to /index.php?q=/init

also if there is an error still, please check index.php settings, to see if it matches this

    // Settings
            $config_str = <<<SETTINGS
    [homepage]
    tasks_to_show = 10

    [tasks]
    lifespan = 1

    [database]
    dsn = sqlite:tasks.sq3
    username = 
    password =
    SETTINGS;
	
	
Installing Taskboard to a linux box via ssh, and making it hidden behind tor
======
in console:
	cd /var/www/
	apt-update
	apt-get install tor apache2 php5 mysql_server mysql_client php5-gd php5-mysql unzip
	wget https://github.com/corneyflorex/TaskBoard/zipball/master --no-check-certificate
	unzip master
	mv corneyflorex-TaskBoard-XXXXXXX/* .
	rm corneyflorex-TaskBoard-XXXXXXX/


edit php.ini (execute 'php --ini' if you cant find it)
make sure it contains the equivelant of (may not be always the same file path, if not just 'find | grep pdo_mysql.so' or the like to find them)
	extension=/usr/lib/php5/20090626/mysql.so
	extension=/usr/lib/php5/20090626/pdo_mysql.so
	extension=/usr/lib/php5/20090626/gd.so

basic gist of mysql setup for taskboard (in mysql console as mysqlroot)
	mysql> CREATE DATABASE taskboard;
	mysql> CREATE USER 'tbuser'@'localhost' IDENTIFIED BY 'SomE_paSs';
	mysql> GRANT ALL PRIVILEGES ON taskboard.* TO 'tbuser'@'localhost';
		
edit settings.php with mysql settings etc.

browse to (in browser) http://[HOST]/?q=/init





other things to make sure of:
	/etc/apache2/mods-enabled contains 'php5.conf' and 'php5.load'
	if not then copy them from /etc/apache2/mods-available

Todo
======
+ Moderation/Admin
+ Messaging
+ Voting
+ Stickied message per tag ( goes to the top of the page on particular tags)