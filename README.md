# Composer and Docker
Creation and implementation of a composer website with PHP and databases.
This website is meant to show the creation of a website using only composer.
## Installing
After installing composer globally, to create the website, first run the following:
```sh
> mkdir /c/sites/composer.loc
> cd /c/sites/composer.loc
> composer init
> mkdir public
> mkdir src
```
- public/ folder is for the entire website pages.
- src/ folder is for libraries and is defined in autoload.

After following the prompts, my composer.json file looked like this:
```sh
{
    "name": "mzimhlem/cartracker",
    "description": "This is a car tracking system using an external API.",
    "license": "proprietary",
    "autoload": {
        "psr-4": {
            "App\\": "src/"
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
The "autoload" adds your libraries as well as maps their root folder, in this case it's the folder src/. 
It's namespace will be "App". After setting this up, there will be no vendor or autoloader file, these two 
will only be added once the first package is installed.
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
We will use Doctrine DBAL for the database abstraction layer and Doctrine ORM for mapping.
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
> composer require doctrine/orm
```
The above will also install dbal package also.
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
#### ORM
This package is installed already but there are 2 more packages that work with it, see below to be installed also:
```sh
> composer require symfony/cache
> composer require doctrine/annotations
```
We have our packages installed, now we will need to create a normal user entity, create an entity manager and utilize it.
This ORM is the same as the one used with symfony:
```sh
<?php
// Include the autoloader.
include_once __DIR__ . '/../vendor/autoload.php';
// Includes
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Entity\Test\User;

// Create a simple "default" Doctrine ORM configuration for Annotations
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), true, null, null, false);

// database configuration parameters
$connectionParams = array(
    'dbname' => 'test',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
// obtaining the entity manager
try {
    $entityManager = EntityManager::create($connectionParams, $config);
} catch (\Exception $e) {
    print_r($e);
    exit;
}

$user = new User();
$user->setName("Mzimhle Mosiwe");
$user->setEmail("mzimhle@gtalk.com");
$user->setCellphone("0735897700");

try {
    $entityManager->persist($user);
    $entityManager->flush();
    echo 'user created: '.$user->getId();
} catch (\Exception $e) {
    print_r($e);
    exit;
}
```
###### Repository
This section allows you to be able to create custom repositories linked to your entity. To set it up:
Add the following to your entity class file.:
```sh
...
use Doctrine\ORM\Mapping as ORM;
...
/**
 * @ORM\Entity(repositoryClass="App\Repository\Test\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
...
```
Now we have the entity linked to a repository, lets setup our repository in the class file
/src/Repository/Test/UserRepository.php:
```sh
namespace App\Repository\Test;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getThemLimited($number = 15)
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->setMaxResults($number);
        return $queryBuilder->getQuery()->getResult();
    }
}
```
#### ORM