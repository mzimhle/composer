# Composer
Creation and implementation of a composer website with PHP and databases.
This website is meant to show the creation of a website using only composer.

## Installing

After installing composer globally, to create the website, first run the following:
```sh
> mkdir /c/sites/composer.loc
> cd /c/sites/composer.loc
> composer init
```
After following the prompts, my composer.json file looked like this:
```sh
{
    "name": "mzimhlem/cartracker",
    "description": "This is a car tracking system using an external API.",
    "license": "proprietary",
    "autoload": {
        "psr-4": {
            "CarTracker\\": "src/"
        }
    },
    "authors": [
        {
            "name": "Mzimhle Mosiwe",
            "email": "mzimhle.mosiwe@gmail.com"
        }
    ],
    "minimum-stability": "dev"
}
```
The "autoload" adds your libraries as well as maps their root folder, in this case its the folder src/.
It's namespace will be "CarTracker".
Install a package:
```sh
> composer require monolog/monolog
```
This will install the autoloader file, the vendor folder as well as the monolog package, if the first two items 
are not there. 
```sh
> composer require monolog/monolog
```
## Re - installing
At this point your application is online on gitHub ( https://github.com/mzimhle/composer ), if I do want to have it on 
another folder, I will need to have this in your .gitignore file:
```sh
/vendor/
composer.json
```
This will ensure that you only have the composer.lock file, so that when you reinstall else where, only packages with 
specific package versions will be installed. So run the following command:
```sh
> composer install
```
## Run a website
Now we have finished configuring our composer website, we need to run it, we will use the built in php to do this, same way
we do with laravel. You must create a public/ folder where the website files will be located, then add an index.php file.
```sh
> php -S 127.0.0.1:8000 -t public/
```
## Connect to a database
We will use Doctrine DBAL for the database abstraction layer. 
```sh
NOTE:

The DBAL (DataBase Abstraction Layer) is a piece of software that simplifies interaction with SQL databases, by allowing
you to use them without worrying about the specific dialects or differences of the different DBMS vendors. It basically 
allows you to run SQL queries against the DBMS without writing vendor specific SQL.

The ORM (Object Relational Mapper) is a tool that gives you the impression of working with an in-memory data structure 
represented as an object graph with associated objects. It simplifies application logic related with SQL operations by 
removing all the SQL and abstracting it into OOP logic. Doctrine 2 ORM simply handles loading and persisting of POPO 
(Plain Old PHP Objects).

https://stackoverflow.com/questions/15127666/object-relational-mapping-vs-database-abstraction-layer
```
#### DBAL
We need to install doctrine dbal first. Remember, this package is used only as a database abstraction only.
```sh
> composer require doctrine/dbal
```
Now to use it your index.php file will look like this.:
```sh
<?php
// Include the autoloader. THIS IS NEEDED ON TOP OF EACH FILE.
require_once __DIR__ . '/../vendor/autoload.php';
// Include database abstraction driver
use \Doctrine\DBAL\DriverManager;
// Connection string to my local database.
$connectionParams = array(
    'dbname' => 'test',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
// Check if all is well.
try {
    $conn = DriverManager::getConnection($connectionParams);

} catch (\Exception $e) {
    print_r($e);
}
// Connection string
$sql = "SELECT * FROM user";
// Create the statement and fetch data.
$stmt = $conn->query($sql);
// Return data.
while (($row = $stmt->fetchAssociative()) !== false) {
    echo $row['name'].'<br />';
}
```

