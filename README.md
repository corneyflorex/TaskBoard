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

Discussion
======
FORUM: http://nero.secondsource.info/p/forum/forum_viewtopic.php?6478.last

Requirments
======

* PHP
* SQLite

Install
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

Todo
======
+ Moderation/Admin
+ Messaging
+ Voting
+ Stickied message per tag ( goes to the top of the page on particular tags)