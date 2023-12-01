<?php

namespace DBManager;

require __DIR__ .'/S3Connector.php';

#use Aws\S3\S3Client;
use Psr\Http\Message\UploadedFileInterface;
use S3Connector;
use DBManager\DBHandler;
use \Psr\Http\Message\ResponseInterface as Response;
use Aws\S3\Exception\S3Exception;

class S3Handler extends S3Connector
{
    private static $instance;

    public function __construct()
    {
        parent::__construct();
    }


    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }



public function uploadImage(UploadedFileInterface $uploadedFile, Response $response,$ipAddress)
{
    $s3Client = self::getInstance()->getS3Client();

    // AWS S3 업로드 로직
    $s3Bucket = 'photo-bucket-test1';
    $s3Folder = 'photo_test/';
    $s3Key = $s3Folder . $uploadedFile->getClientFilename();

    try {
        $result = $s3Client->putObject([
            'Bucket' => $s3Bucket,
            'Key'    => $s3Key,
            'Body'   => $uploadedFile->getStream(),
        ]);

        // 추가된 로깅
        error_log("IP Address: " . $ipAddress);

        if ($result) {
            // 이미지 업로드가 성공하면 DB에 메타데이터 및 키오스크 IP 저장
            $dbHandler = new DBHandler();
            error_log("Registering kiosk...");
            $ipIdx = $dbHandler->registerKiosk($ipAddress);

            // 추가된 로깅
            error_log("IP Index from registerKiosk: " . $ipIdx);
            
            #$ipIdx = $dbHandler->getIpIdxByIp($kioskIp); // 키오스크 IP로부터 ip_idx를 얻어옴
            error_log("Saving metadata...");
            $dbHandler->saveMetadata($uploadedFile->getClientFilename(), $s3Key, $ipIdx);
            
            $dbHandler->updateTotalLog($ipIdx, 1, 1, 1, 1);

        // 직접 JSON 응답 작성
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } else {
        throw new \Exception("S3 upload failed");
    }
    } catch (\Exception $e) {
        // S3 업로드 실패 시 에러 응답
        return ['success' => false, 'error' => 'Failed to upload image.'];
    }
}

public function getImageData($imageKey)
    {
        try {
            // S3에서 이미지 데이터 가져오기
            $s3Bucket = 'photo-bucket-test1';

            $result = $this->s3Client->getObject([
                'Bucket' => $s3Bucket,
                'Key'    => $imageKey,
            ]);

            // 이미지 데이터를 반환
            return $result['Body']->getContents();
        } catch (\Exception $e) {
            // 예외 처리
            echo 'Error fetching image from S3: ' . $e->getMessage();
            return null;
        }
    }

}

   /* public function uploadImage(UploadedFileInterface $uploadedFile, Response $response)
{
    $s3Client = self::getInstance()->getS3Client();

    // AWS S3 업로드 로직
    $s3Bucket = 'photo-bucket-test1';
    $s3Folder = 'photo_test/';
    $s3Key = $s3Folder . $uploadedFile->getClientFilename();


try {
    $result = $s3Client->putObject([
        'Bucket' => $s3Bucket,
        'Key'    => $s3Key,
        'Body'   => $uploadedFile->getStream(),
    ]);

    if ($result) {
        // 이미지 업로드가 성공하면 DB에 메타데이터 저장
        $dbHandler = new DBHandler();
        $dbHandler->saveMetadata($uploadedFile->getClientFilename(), $s3Key);
        $response->getBody()->write(json_encode(['message' => 'Image uploaded successfully.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            throw new \Exception("S3 upload failed");
        }
} catch (\Exception $e) {
    // S3 업로드 실패 시 에러 응답
    $response->getBody()->write(json_encode(['error' => 'Failed to upload image.']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
}
}*/
?>