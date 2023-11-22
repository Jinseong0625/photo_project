<?php

namespace DBManager;

require __DIR__ .'/S3Connector.php';

use Aws\S3\S3Client;
use Psr\Http\Message\UploadedFileInterface;
use S3Connector;
use DBManager\DBHandler;

class S3Handler extends S3Connector
{
    private $s3Connector;

    public function __construct()
    {
        parent::__construct();
        $this->s3Connector = new S3Connector();
    }

    public function uploadImage(UploadedFileInterface $uploadedFile)
{
    $s3Client = $this->s3Connector->getS3Client();

    // AWS S3 업로드 로직
    $s3Bucket = 'photo-bucket-test1';
    $s3Folder = 'photo_test/';
    $s3Key = $s3Folder . $uploadedFile->getClientFilename();

    $result = $s3Client->putObject([
        'Bucket' => $s3Bucket,
        'Key'    => $s3Key,
        'Body'   => $uploadedFile->getStream(),
    ]);

    if ($result) {
        // 이미지 업로드가 성공하면 DB에 메타데이터 저장
        $dbHandler = new DBHandler();
        $dbHandler->saveMetadata($uploadedFile->getClientFilename(), $s3Key);
        echo "Image uploaded successfully.";
    } else {
        echo "Image upload failed.";
    }
}

    public function downloadImage($fileName)
    {
        $s3Client = $this->s3Connector->getS3Client();

        // AWS S3 다운로드 로직
        $s3Bucket = 'photo-bucket-test1';
        $s3Key = 'photo_test/' . $fileName;

        $result = $s3Client->getObject([
            'Bucket' => $s3Bucket,
            'Key'    => $s3Key,
        ]);

        return [
            'error' => 'E0000',
            'data'  => $result['Body'],
        ];
    }

}
?>