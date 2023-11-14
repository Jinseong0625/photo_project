<?php 
require __DIR__ . '/global_var.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ .'/DBHandler.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DBManager\DBHandler;

$app = AppFactory::create();
$api = new DBHandler();

$app->addBodyParsingMiddleware();
#$app->setBasePath("/test");

session_start();


$app->get('/meta/ip', function ($request, $response, $args) use($api) {
	$row = $api->sp_select_ipadd();
	$response->getBody()->write($row);
	return $response;
});

$app->get('/test', function ($request, $response, $args) use($api) {
	$row = $api->sp_select_test();
	$response->getBody()->write($row);
	return $response;
});

$app->post('/ip', function ($request, $response, $args) use($api) 
{
	$params = $request->getParsedBody();
	$ip_add = $params['ip_address'];
	if($ip_add == null)
	{
		$json_data = array
        (
            "error" => "E1003",
            "data" => ""
        );

		$row = json_encode($json_data);
	}
	else
	{
		$row = $api->sp_insert_ipadd($ip_add);

		if (is_array($row)) {
			$row = json_encode($row);
		}
	}
	
	$response->getBody()->write($row);
	return $response;
});

$app->post('/ulog', function ($request, $response, $args) use($api) 
{
	$params = $request->getParsedBody();
	$ipidx = $params['ipidx'];
	$photo_url = $params['photo_url'];
	$user_cnt = $params['user_cnt'];
	if($ipidx == null)
	{
		$json_data = array
        (
            "error" => "E1003",
            "data" => ""
        );

		$row = json_encode($json_data);
	}
	else
	{
		$row = $api->sp_insert_UserLog($ipidx,$photo_url,$user_cnt);

		if (is_array($row)) {
			$row = json_encode($row);
		}
	}
	
	$response->getBody()->write($row);
	return $response;
});

$app->run();

?>