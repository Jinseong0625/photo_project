<?php 
require __DIR__ . '/global_var.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ .'/DBHandler.php';
require __DIR__ . '/S3Handler.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DBManager\DBHandler;
use DBManager\S3Handler;

$app = AppFactory::create();
$container = $app->getContainer();

$container['db'] = function (ContainerInterface $container){
	return new DBConnector();
};

$container['s3'] = function (ContainerInterface $container){
	return new S3Connector();
};

$api = new DBHandler();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);
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

// 
$app->get('/ulog/{uidx}', function ($request, $response, $args) use($api) 
{	
	$uidx = $request->getAttribute('uidx');
	$row = $api->sp_select_UserLog($uidx);
	$response->getBody()->write($row);
	return $response;
});

$app->get('/alltotal/{ipidx}', function ($request, $response, $args) use($api) 
{	
	$ipidx = $request->getAttribute('ipidx');
	$row = $api->sp_select_TotalLog_All($ipidx);
	$response->getBody()->write($row);
	return $response;
});

$app->get('/daytotal/{ipidx}', function ($request, $response, $args) use($api) 
{	
	$ipidx = $request->getAttribute('ipidx');
	$row = $api->sp_select_Total_day($ipidx);
	$response->getBody()->write($row);
	return $response;
});

$app->get('/weektotal/{ipidx}', function ($request, $response, $args) use($api) 
{	
	$ipidx = $request->getAttribute('ipidx');
	$row = $api->sp_select_total_week($ipidx);
	$response->getBody()->write($row);
	return $response;
});

$app->get('/monthtotal/{ipidx}', function ($request, $response, $args) use($api) 
{	
	$ipidx = $request->getAttribute('ipidx');
	$row = $api->sp_select_Total_month($ipidx);
	$response->getBody()->write($row);
	return $response;
});

$app->get('/yeartotal/{ipidx}', function ($request, $response, $args) use($api) 
{	
	$ipidx = $request->getAttribute('ipidx');
	$row = $api->sp_select_Total_year($ipidx);
	$response->getBody()->write($row);
	return $response;
});

// 키오스크 등록 API
$app->post('/kioskip', function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();

    // Check if required parameters are present
    if (isset($parsedBody['ip_address'])) {
        $ipAddress = $parsedBody['ip_address'];
        
        // Save the kiosk IP address to the database and retrieve ip_idx
        $dbHandler = new DBHandler();
        $ipIdx = $dbHandler->registerKiosk($ipAddress);

        if ($ipIdx !== null) {
            // 직접 JSON 응답 작성
            $response->getBody()->write(json_encode(['message' => 'Kiosk registered successfully.', 'ip_idx' => $ipIdx]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            // Handle the case where ip_idx is not available
            $response->getBody()->write(json_encode(['error' => 'Failed to retrieve ip_idx.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    } else {
        // 직접 JSON 응답 작성
        $response->getBody()->write(json_encode(['error' => 'Missing required parameters.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
});

// 이미지 업로드 API
$app->post('/upload', function (Request $request, Response $response, array $args) {
    $uploadedFiles = $request->getUploadedFiles();

    // Check if image file is uploaded
    if (isset($uploadedFiles['image'])) {
        $imageHandler = new \DBManager\S3Handler();
        $result = $imageHandler->uploadImage($uploadedFiles['image'], $response , $_SERVER['REMOTE_ADDR']);
        return $result;

        if ($result['success']) {
            // 이미지 업로드 및 메타데이터 저장이 성공하면 응답
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode(['message' => 'Image uploaded successfully.']));
        } else {
            // 실패 시 에러 응답
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode(['error' => 'Failed to upload image.']));
        }
    } else {
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode(['error' => 'No image file uploaded.']));
    }
});

// 이미지 다운로드 API
/*$app->get('/download/{fileName}', function (Request $request, Response $response, array $args) use ($container) {
    $fileName = $args['fileName'];

    // 컨테이너에서 DB 객체 가져오기
    #$db = $request->getAttribute('db');
	
	$db = $container->get('DBHandler');
	$s3Handler = $container->get('s3Handler');

    // 트랜잭션 시작
    $db->beginTransaction();

    try {
        $result = $s3Handler->downloadImage($fileName);

        if ($result['error'] === null) {
            // 이미지 다운로드 성공
            $response->getBody()->write($result['data']);

            // 파일 다운로드 성공 시 상태 업데이트
            $db->updateFileStatus($fileName, 1);

            // 트랜잭션 커밋
            $db->commit();

            return $response->withHeader('Content-Type', 'image/jpeg');
        } else {
            // 이미지 다운로드 실패
            $db->rollBack();

            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode(['error' => 'Image not found.']));
        }
    } catch (\Exception $e) {
        // 예외 발생 시 롤백
        $db->rollBack();

        return $response->withStatus(500)->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode(['error' => 'Internal server error.']));
    }
});*/

// 이미지 다운로드 API
/*$app->get('/download', function (Request $request, Response $response, array $args) {
    #$imageKey = $request->getQueryParams()['imageKey'] ?? null;
    $path = $request->getUri()->getPath();

    // path에서 imageKey 값을 추출
    $parts = explode('/', $path);
    $imageKey = end($parts);

    var_dump($request->getQueryParams());

    if (!$imageKey) {
        // 필수 파라미터가 누락됨
        $response->getBody()->write(json_encode(['error' => 'Missing imageKey parameter.','imageKey' => $imageKey]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $s3Handler = S3Handler::getInstance();
    $s3Client = $s3Handler->getS3Client();
    $s3Bucket = 'photo-bucket-test1';

    // 수정된 부분: S3 스토리지에서 직접 이미지 데이터 가져오기
    $imageDataFromS3 = $s3Handler->getImageData($imageKey);

    if (!$imageDataFromS3) {
        // 이미지 정보를 찾을 수 없음
        $response->getBody()->write(json_encode(['error' => 'Image not found.']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    // 파일의 MIME 타입 확인
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $imageDataFromS3);
    finfo_close($finfo);

    // 파일 다운로드 헤더 설정
    $response = $response->withHeader('Content-Type', $mimeType);
    $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $imageKey . '"');

    // S3에서 가져온 이미지를 클라이언트로 전송
    $response->getBody()->write($imageDataFromS3);
    return $response;
});*/

// 이미지 다운로드 API
$app->get('/download/{filename}', function (Request $request, Response $response, array $args) {
    $filename = $args['filename'];

    if (!$filename) {
        // 필수 파라미터가 누락됨
        $response->getBody()->write(json_encode(['error' => 'Missing filename parameter.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $s3Handler = S3Handler::getInstance();
    $s3Client = $s3Handler->getS3Client();
    $s3Bucket = 'photo-bucket-test1';

    // 수정된 부분: S3 스토리지에서 직접 이미지 데이터 가져오기
    $imageDataFromS3 = $s3Handler->getImageData("photo_test/{$filename}");

    if (!$imageDataFromS3) {
        // 이미지 정보를 찾을 수 없음
        $response->getBody()->write(json_encode(['error' => 'Image not found.']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    // 파일의 MIME 타입 확인
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $imageDataFromS3);
    finfo_close($finfo);

    // 파일 다운로드 헤더 설정
    $response = $response->withHeader('Content-Type', $mimeType);
    $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

    // S3에서 가져온 이미지를 클라이언트로 전송
    $response->getBody()->write($imageDataFromS3);

    // 파일 상태를 업데이트
    $dbHandler = new DBHandler();
    $dbHandler->updateFileStatus($filename);

    return $response;
});



$app->run();

?>