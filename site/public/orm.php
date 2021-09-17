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
    'host' => 'db',
    'driver' => 'pdo_mysql',
);
// obtaining the entity manager
try {
    $entityManager = EntityManager::create($connectionParams, $config);
} catch (\Exception $e) {
    print_r($e);
    exit;
}
echo "data inserted";
exit;

// Below is an example to insert.
$user = new User();
$user->setName("Mzimhle");
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

// Below is an example to insert.
$user = new User();
$user->setName("Sakile");
$user->setEmail("sakile@gtalk.com");
$user->setCellphone("0735897701");

try {
    $entityManager->persist($user);
    $entityManager->flush();
    echo 'user created: '.$user->getId();
} catch (\Exception $e) {
    print_r($e);
    exit;
}