taskwarrior-php 0.1
===================

A very simple PHP Front End Viewer for Task Warrior

For now this viewer, display each task, sorted by projects, their descriptions, ages and tags. From the viewer, it is also possible to mark a task as done and delete it. It is also possible to insert a new task. 

Keep in mind that this application is still in version 0.1 and it is quite possible that there are still bugs. 

Installation
------------

To install this application and use it as a viewer, you just have to copy the entire folder into a PHP server. Then, the config.php must be edited to reflect the place where the taskwarrior files are located. 

You have to put your taskwarrior files (pending.data and completed.data) on the server. For that, you can use the integrated FTP push and pull requests to synchronize your tasks. 
