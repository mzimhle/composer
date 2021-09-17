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
Now we have the entity linked to a repository, lets set up our repository in the class file
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
#### Docker
An image is a class definition where we can define properties and behaviour, containers are 
merely instances of this class. Custom docker images are created using the Docker file.
Before we start with creating an image, we need to set up its apache configuration that we will copy to the image
on a separate conf file.
Our conf file is:
```sh
<VirtualHost *:80>
  ServerAdmin webmaster@localhost
  DocumentRoot /var/www/public

  <Directory /var/www>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```
The current Dockerfile contains:
```sh
# Image we are using as well as the version
FROM php:7.4-apache
# Set up our environmental variables
ENV MYSQL_ROOT_USER=root
ENV MYSQL_ROOT_PASSWORD=""
# Copy the src library files for the site to the container.
COPY site/src /var/www/html/src
COPY site/public /var/www/html/public
# Bring composer binary into the PHP container. Basically installing composer to your image. This is version 2.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY site/composer.json /var/www/html/composer.json
COPY site/composer.lock /var/www/html/composer.lock
# Define our apache configuration file, copying it to the relevant path
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf
# Run commands for this Dockerfile
# Update apt-get then install unzip and zip
# Install composer packages from the composer.json file
RUN apt-get update && apt-get install -y \
    unzip \
    zip && composer install
# Expose the port 80 for this image
EXPOSE 80
```
After creating the above dockerfile, we need to build out image so that it's available for containers we will create.
Please note that the last parameter which is "." is to specify where the Dockfile is located, which in this case 
is the current directory, the -t is to name the image, in this case the name is composer_loc.
```sh
> docker build -t composer_loc .
```
After running the above, the image will be available under "images" in the docker application. or check by running:
```sh
> docker images
```
To access the image as well as make sure all files are copied correctly, you can access its commandline, I am using
"winpty" because of my bash commandline application which is MINGW64, you can leave it out if you are using another one:
```sh
> winpty docker run -it composer_loc bash
```
#### Docker-Compose
This is a tool for running multiple container docker applications. You use a YAML file to configure it.
The YAML file is the docker-compose.yml file. This is how ours look:
```sh
> winpty docker run -it composer_loc bash
```