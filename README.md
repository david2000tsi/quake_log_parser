# Quake log parser

This is a basic Quake log parser.

You can see a brief description of each file of this project bellow:

You should have installed in your computer the PHP, PHPUnit and MySQL to run example and test files.

### games.log

The Quake log file used by the parser.

### Parser.php

The php parser for log file.

### ParserTest.php

Unit tests for parser, run tests with:

```
cd <project_path>
phpunit ParserTest.php
```

### Database.php

The php database class to generate structure and run queries, the connection parameters is in this file.

### database.sql

The sql scripts used in the Database class (this file is not necessary for Database class).

### DatabaseTest.php

Unit tests for database class, run tests with:

```
cd <project_path>
phpunit DatabaseTest.php
```

### Example.php

Basic example explaining how to use parser. Run Example.php file and analyse the output.

This file will display ranking of kills of each match, generate database structure and fill tables with log data.

```
cd <project_path>
php Example.php
```
