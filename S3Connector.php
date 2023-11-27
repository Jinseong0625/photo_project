<?php
require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class S3Connector {
    protected $s3Client;

    function __construct() {
        $this->s3Client = $this->initializeS3();
    }

    private function initializeS3() {
        // AWS SDK를 사용하여 S3 클라이언트 초기화
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'ap-northeast-2', // 예: us-east-1
            'credentials' => [
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            ],
        ]);

        return $s3;
    }

    public function getS3Client() {
        return $this->s3Client;
    }
}

?>
