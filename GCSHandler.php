<?php

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Storage\StorageObject;
use Psr\Http\Message\UploadedFileInterface;
use DBManager\DBHandler;

class GCSHandler extends GCSConnector {
    private static $instance;

    public function __construct($projectId, $keyFilePath) {
        parent::__construct($projectId, $keyFilePath);
    }

    public static function getInstance($projectId, $keyFilePath) {
        if (!isset(self::$instance)) {
            self::$instance = new self($projectId, $keyFilePath);
        }
        return self::$instance;
    }

    public function uploadImage(UploadedFileInterface $uploadedFile, $ipAddress, $bucketName) {
        $storage = $this->getStorageClient();
        $bucket = $storage->bucket($bucketName);

        // 업로드할 파일의 키 생성
        $gcsKey = 'your-prefix/' . uniqid() . '/' . $uploadedFile->getClientFilename();

        // StorageObject 생성
        $object = $bucket->upload(
            $uploadedFile->getStream(),
            [
                'name' => $gcsKey,
            ]
        );

        try {
            $bucket->upload(
                $uploadedFile->getStream(),
                [
                    'name' => $gcsKey,
                ]
            );


            // 추가된 코드: 이미지 업로드 성공 시 메타데이터 저장
            $metadata = [
                'ip_address' => $ipAddress,
                'uploaded_at' => time(), // 업로드 시간 저장 (timestamp 형식)
            ];

            $object->update(['metadata' => $metadata]);

            // ... (이하 코드는 이전 코드와 동일하게 유지)

        } catch (GoogleException $e) {
            // Google Cloud Storage 업로드 실패 시 에러 응답
            $errorMessage = 'Failed to upload image. ' . $e->getMessage();
            $dbHandler = new DBHandler();
            $dbHandler->logError('GOOGLE_STORAGE_UPLOAD_ERROR', $errorMessage, $ipAddress);
            return ['success' => false, 'error' => 'Failed to upload image.'];
        }
    }

    public function getImageData($gcsKey, $bucketName) {
        $storage = $this->getStorageClient();
        $bucket = $storage->bucket($bucketName);

        // StorageObject 생성
        $object = $bucket->object($gcsKey);

        try {
            $imageDataFromStorage = $object->downloadAsString();

            // 추가된 코드: 이미지 데이터를 가져온 후의 작업
            // 예시: 가져온 데이터를 가공하거나 로그를 남기는 등의 작업 수행

            return $imageDataFromStorage;
        } catch (GoogleException $e) {
            // Google Cloud Storage에서 이미지 데이터 가져오기 실패 시 에러 응답
            echo 'Error fetching image from Google Cloud Storage: ' . $e->getMessage();
            return null;
        }
    }
}
?>
