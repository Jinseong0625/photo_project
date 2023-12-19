<?php
require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class S3Connector {
    protected $s3Client;

    function __construct() {
        $this->s3Client = $this->initializeS3();
    }

    private function initializeS3() {
        // 환경 변수에서 AWS 액세스 키 및 시크릿 키 가져오기
        $awsAccessKey = getenv('AWS_ACCESS_KEY');
        $awsSecretKey = getenv('AWS_SECRET_KEY');

        // 값이 설정되어 있는지 확인
        if ($awsAccessKey === false || $awsSecretKey === false) {
            // 에러를 로그에 기록하거나 출력
            error_log("Error: AWS_ACCESS_KEY or AWS_SECRET_KEY is not set.");
            // 또는 화면에 에러 메시지를 출력할 수 있습니다.
            // echo "Error: AWS_ACCESS_KEY or AWS_SECRET_KEY is not set.";
            return null;
        }
        // AWS SDK를 사용하여 S3 클라이언트 초기화
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'ap-northeast-2', // 예: us-east-1
            'credentials' => [
                #'key'    => AWS_ACCESS_KEY,
                #'secret' => AWS_SECRET_KEY,
                'key'    => $awsAccessKey,
                'secret' => $awsSecretKey,
            ],
        ]);

        return $s3;
    }

    public function getS3Client() {
        return $this->s3Client;
    }
}

?>
