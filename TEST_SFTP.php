<?php

#use phpseclib\Net\SFTP;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;

require __DIR__ . '/vendor/autoload.php'; // Composer autoload 파일
require __DIR__ . '/global_var.php';

// 업로드, 다운로드 함수 적용시
function uploadFile(SFTP $sftp, $localFilePath, $remoteFilePath)
{
    if ($sftp->put($remoteFilePath, $localFilePath, SFTP::SOURCE_LOCAL_FILE)) {
        echo "File uploaded successfully.\n";
    } else {
        echo "File upload failed.\n";
    }
}

function downloadFile(SFTP $sftp, $remoteFilePath, $localFilePath)
{
    if ($sftp->get($remoteFilePath, $localFilePath)) {
        echo "File downloaded successfully.\n";
    } else {
        echo "File download failed.\n";
    }
}

// SFTP 연결 정보
$sftpHost = _HOST_;
$sftpPort = 22;
$sftpUsername = SFTP_NAME;
$pemFilePath = 'C:/Users/chomk/Downloads/FirstTestKey.pem';

// SFTP 객체 생성
$sftp = new SFTP($sftpHost, $sftpPort);

// RSA 객체 생성 및 키 로드1
#$key = new RSA();
#$key->load(file_get_contents($pemFilePath));

$key = PublicKeyLoader::load(file_get_contents($pemFilePath));

// SFTP 연결
if (!$sftp->login($sftpUsername, $key)) {
    exit('Login Failed');
}

// 업로드
$localFile = 'C:/Users/chomk/Downloads/image.png';
$remoteFile = '/home/ubuntu/image/image.png';
uploadFile($sftp, $localFileForUpload, $remoteFileForUpload);

/*if ($sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE)) {
    echo "File uploaded successfully.\n";
} else {
    echo "File upload failed.\n";
} */

/*
// 다운로드
$localDownloadFile = 'C:/Users/chomk/Downloads/image.png';
$remoteDownloadFile = '/home/ubuntu/image.png';

if ($sftp->get($remoteDownloadFile, $localDownloadFile)) {
    echo "File downloaded successfully.\n";
} else {
    echo "File download failed.\n";
}

// 파일명을 직접 입력 받는 스타일에 코드

// 다운로드할 파일 입력 받기
echo "Enter the remote file to download: ";
$remoteDownloadFile = trim(fgets(STDIN));

// 로컬에 저장할 파일 입력 받기
echo "Enter the local file path to save: ";
$localDownloadFile = trim(fgets(STDIN));

// 다운로드
if ($sftp->get($remoteDownloadFile, $localDownloadFile)) {
    echo "File downloaded successfully.\n";
} else {
    echo "File download failed.\n";
}


*/
// SFTP 연결 닫기
$sftp->disconnect();

?>