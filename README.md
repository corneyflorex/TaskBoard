About
======
This will be an international small task board website for anons around the world. 
It shall require no registration or signing in, and shall allow for anons to post up small task.
For example "Make poster for #OpTaskForceAlpha ". 
As well as allow other anons to alert if they are interested, or to submit a message or upload a file on completion of a posted task. 
Also allow for searching or sorting task by language, country, or tags (or a combination of each).

So basically anyone can create an AnonTask, spammers kept out with captcha

The task starts at the top of the RECENT TASK board, and slowly moves down as other tasks generate participants or new tasks are posted

If a task gets a participant, it moves to the top of the RECENT TASKS board, if tthe task gets no participants it moves down and down the page until it dies off after 24 hours if it doesnt become "Active".

A task becomes "Active" when it generates over 100 participants in a single day. And moves from the RECENT TASK's into active tasks until it doesn't generate enough participants per day - it then drops back to recent tasks.

No Mods

No Tracking

Clean & Simple

Discussion
======
FORUM: http://nero.secondsource.info/p/forum/forum_viewtopic.php?6478.last


Install
======
Copy this folder to your PHP and SQLite enabled webserver. To insert test data (you may need to do this first, if it throws an error), type index.php?q=/init

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