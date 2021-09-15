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

$users = $entityManager->getRepository(User::class)->getThemLimited();

foreach ($users as $user) {
    echo $user->getName().'<br />';
}