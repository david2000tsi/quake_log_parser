# Quake log parser

This is a basic Quake log parser.

You can see a brief description of each file of this project bellow:

You should have installed in your computer the PHP, PHPUnit and MySQL to run example and test files.

### games.log

The Quake log file used by the parser.

### src/Parser.php

The php parser for log file.

### test/ParserTest.php

Unit tests for parser, run tests with:

```
cd <project_path>/test
phpunit ParserTest.php
```

### src/Database.php

The php database class to generate structure and run queries, the connection parameters is in this file.

### sql/database.sql

The sql scripts used in the Database class (this file is not necessary for Database class).

### test/DatabaseTest.php

Unit tests for database class, run tests with:

```
cd <project_path>/test
phpunit DatabaseTest.php
```

### Example.php

Basic example explaining how to use parser. Run Example.php file and analyse the output.

This file will display ranking of kills of each match, generate database structure and fill tables with log data.

```
cd <project_path>
php Example.php
```

Use the following command to save output in txt file:

```
php Example.php > output.txt
```

### web/app.php

Basic web app to see user list and kill score and find user by name.
Follow the steps bellow to run app (run Example.php file before to create and populate database):

Change to project path and starts php server:
```
cd <project_path>
php -S <ip>:<port>

```

Open the browser (teste in chrome and forefox) and access with:

```
<ip>:<port>/web/app.php (e.g. localhost:8080/web/app.php)
```

### web/utils.php

File used by app.php.

### web/poison.svg

File used by app.php.

### web/template.jpg

File used as template to develop web app.

## The games.log, poison.svg and template.jpg was copied from:

```
https://gist.github.com/labmorales/7ebd77411ad51c32179bd4c912096031
```
