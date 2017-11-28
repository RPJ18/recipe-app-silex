<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

require '../vendor/autoload.php';
require '../src/Controllers/Recipes.php';

$app = new Application();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__ . '/../templates']);
$app->register(new Silex\Provider\AssetServiceProvider(), [
	'assets.version' => 'v1',
    'assets.version_format' => '%s?version=%s'
]);
$app->register(new Silex\Provider\DoctrineServiceProvider(), [
	'db.options' => [
		'driver' => 'pdo_mysql',
		'host' => '127.0.0.1',
		'dbname' => 'cookbook',
		'user' => 'root',
		'password' => 4682726,
	]
]);

// show all the recipes
$app->get('/', 'CookBook\\Controllers\\Recipes::getAll')->bind('home');

// get form to create recipe
$app->get('/recipes', 'CookBook\\Controllers\\Recipes::getNewForm')->bind('new_recipe');

// sent the form
$app->post('/recipes', 'CookBook\\Controllers\\Recipes::create')->bind('recipes');

// show a single recipe
$app->get('/recipes/{id}', 'CookBook\\Controllers\\Recipes::show')->bind('show');

// delete recipe
$app->get('/recipes/{id}/delete', 'CookBook\\Controllers\\Recipes::delete')->bind('delete');

// get form to edit a recipe
$app->get('/edit/{id}', 'CookBook\\Controllers\\Recipes::getEditForm')->bind('edit_recipe');

// sent the form to update the recipe
$app->post('/update/{id}', 'CookBook\\Controllers\\Recipes::update')->bind('update');

Request::enableHttpMethodParameterOverride();

$app->run();