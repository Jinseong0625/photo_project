<?php

#use phpseclib\Net\SFTP;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;

require __DIR__ . '/vendor/autoload.php'; // Composer autoload 파일
require __DIR__ . '/global_var.php';

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
$remoteFile = '/home/ubuntu/image.png';

if ($sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE)) {
    echo "File uploaded successfully.\n";
} else {
    echo "File upload failed.\n";
}
/*
// 다운로드
$localDownloadFile = 'C:/Users/chomk/Downloads/image.png';
$remoteDownloadFile = '/home/ubuntu/image.png';

if ($sftp->get($remoteDownloadFile, $localDownloadFile)) {
    echo "File downloaded successfully.\n";
} else {
    echo "File download failed.\n";
}
*/
// SFTP 연결 닫기
$sftp->disconnect();

?>