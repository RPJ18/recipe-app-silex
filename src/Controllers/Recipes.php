<?php 
namespace CookBook\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Recipes {
	public function getAll(Application $app): string {
		$recipes = $app['db']->fetchAll('SELECT * FROM recipes');

		return $app['twig']->render('home.twig', ['recipes' => $recipes]);
	}

	public function getNewForm(Application $app): string {
		return $app['twig']->render('new_recipe.twig');
	}

	public function create(Application $app, Request $request) {
		$params = $request->request->all();
		$errors = [];

		if(empty($params['name'])) {
			$errors = 'Name cannot be empty';
		}
		if(empty($params['ingredients'])) {
			$errors = 'Ingredients cannot be empty';
		}
		if(empty($params['instructions'])) {
			$errors = 'Instructions cannot be empty';
		}
		if($params['time'] <= 0) {
			$errors = 'Time cannot be negative';
		}

		if(!empty($errors)) {
			$params = array_merge($params, ['errors' => $errors]);
			return $app['twig']->render('new_recipe.twig', $params); 
		}

		$sql = 'INSERT INTO recipes (name, ingredients, instructions, time) VALUES (:name, :ingredients, :instructions, :time)';
		$result = $app['db']->executeUpdate($sql, $params);

		if(!$result) {
			$params = array_merge($params, ['errors' => $errors]);
			return $app['twig']->render('new_recipe.twig', $params); 
		}

		return $app->redirect('/');
	}

	public function show(Application $app, $id): string {
		$sql = 'SELECT * FROM recipes WHERE id = ?';

		$recipe = $app['db']->fetchAssoc($sql, array((int) $id));

		return $app['twig']->render('show.twig', ['recipe' => $recipe]);
	}

	public function getEditForm(Application $app, $id): string {
		$sql = 'SELECT * FROM recipes WHERE id = ?';
		$recipe = $app['db']->fetchAssoc($sql, array((int) $id));

		return $app['twig']->render('edit_recipe.twig', ['recipe' => $recipe]);
	}

	public function update(Application $app, Request $request, $id):string {
		$name = $request->get('name');
		$ingredients = $request->get('ingredients');
		$instructions = $request->get('instructions');
		$time = $request->get('time');

		$sql = 'UPDATE recipes SET name = ?, ingredients = ?, instructions = ?, time = ? WHERE id = ?';

		$result = $app['db']->executeUpdate($sql, array((string) $name, (string) $ingredients, (string) $instructions, (int) $time,(int) $id));

		return $app->redirect('/');
	}

	public function delete(Application $app, $id): string {
		$sql = 'DELETE FROM recipes WHERE id = ?';

		$app['db']->executeUpdate($sql, array((int) $id));
		return $app->redirect('/');
	}
}