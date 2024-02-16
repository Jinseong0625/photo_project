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
        $gcsKey = 'photo_test/' . $uploadedFile->getClientFilename();

        try {

            error_log('Debug: IP Address - ' . $ipAddress);
            error_log('Debug: GCS Key - ' . $gcsKey);


            $object = $bucket->upload(
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
            return ['success' => true, 'gcsKey' => $gcsKey];
        } catch (GoogleException $e) {

            error_log('Error: ' . $e->getMessage());
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

            // 예시: 가져온 데이터를 가공하거나 로그를 남기는 등의 작업 수행

            return $imageDataFromStorage;
        } catch (GoogleException $e) {
            // Google Cloud Storage에서 이미지 데이터 가져오기 실패 시 에러 응답
            echo 'Error fetching image from Google Cloud Storage: ' . $e->getMessage();
            return null;
        }
    }

    public function sendDownloadSignal($gcsKey, $target){
    
        $endpointUrl = '34.64.137.179';

        // cURL 핸들 초기화
        $ch = curl_init();

        $data = [
            'gcskey' => $gcsKey,
            'target' => $target,
        ];

         // cURL 옵션 설정
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);  // URL 설정
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        // cURL 실행 및 응답 획득
    $response = curl_exec($ch);

    // cURL 에러 핸들링
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
        // 에러 핸들링 로직을 추가하거나 예외를 던질 수 있습니다.
    }

    // cURL 세션 종료
    curl_close($ch);

    }
}
?>
