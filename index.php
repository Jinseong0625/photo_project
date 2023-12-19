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

$app->get('/image', function ($request, $response, $args) use($api) {
	$row = $api->sp_select_image();
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
    $ipAddress = $request->getHeader('X-Forwarded-For')[0] ?? $_SERVER['REMOTE_ADDR'];
    $uploadedFiles = $request->getUploadedFiles();

    // Check if image file is uploaded
    if (isset($uploadedFiles['image'])) {
        $imageHandler = new \DBManager\S3Handler();
        
        try {
            $result = $imageHandler->uploadImage($uploadedFiles['image'],$ipAddress );#$_SERVER['REMOTE_ADDR']);

            if ($result['success']) {
                // 이미지 업로드 및 메타데이터 저장이 성공하면 응답
                $response->getBody()->write(json_encode(['message' => 'Image uploaded successfully.']));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            } else {
                // 실패 시 에러 응답
                $response->getBody()->write(json_encode(['error' => 'Failed to upload image.']));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        } catch (\Exception $e) {
            // 예외 처리 시 에러 응답
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    } else {
        // 클라이언트 오류 응답: 필수 파일 누락
        $response->getBody()->write(json_encode(['error' => 'No image file uploaded.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

// 이미지 다운로드 API - 단순하게 파일 이름으로 파일을 다운로드 받을 수 있는 api
// 단점은 중복 처리가 제대로 이루어 지지 못함
$app->get('/download/{filename}', function (Request $request, Response $response, array $args) use($api) {
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
    $api->updateFileStatus($filename);

    return $response;
});

// 호출시 편집이 필요한 파일이 존재할 경우 DB에서 키값이 낮은 순서대로 사진 데이터를
// 다운로드 받게 하는 api 누끼따는 서버에서 이걸 편집이 끝났을때 이걸 호출하면
// 편집이 필요한 사진 파일을 바로 받을 수 있는거임
$app->get('/download', function (Request $request, Response $response, array $args) use($api) {
    try {
        // 수정된 부분: status가 0이면서 가장 낮은 ud_idx의 filename 가져오기
        $filename = $api->getPendingFile();

        if (!$filename || !is_array($filename) || !isset($filename['filename'])) {
            // 편집이 필요한 파일이 없음 또는 $filename이 유효하지 않음
            error_log("No pending file for editing. Filename: " . print_r($filename, true));
            $response->getBody()->write(json_encode(['error' => 'No pending file for editing.']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }        

        $s3Handler = S3Handler::getInstance();
        $s3Bucket = 'photo-bucket-test1';

        // S3에서 가져온 이미지를 클라이언트로 전송
        $imageDataFromS3 = $s3Handler->getImageData("photo_test/" . $filename['filename']);

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
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . basename($filename['filename']) . '"');

        // S3에서 가져온 이미지를 클라이언트로 전송
        $response->getBody()->write($imageDataFromS3);

        // 파일 상태를 업데이트
        try {
            $api->updateFileStatus($filename['filename']);
        } catch (\PDOException $e) {
            // Handle the exception, log the error, or perform other actions as needed.
            error_log('Error updating file status: ' . $e->getMessage());
        }

        return $response;
    } catch (\Exception $e) {
        $errorMessage = 'Error during image download. ' . $e->getMessage();
        $api->logError('IMAGE_DOWNLOAD_ERROR', $errorMessage, $_SERVER['REMOTE_ADDR']);
        // Handle the exception as needed, e.g., log the error.
        echo 'Error: ' . $e->getMessage();
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->run();

?>