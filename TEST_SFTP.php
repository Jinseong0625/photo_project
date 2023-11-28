<?php

#use phpseclib\Net\SFTP;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\RSA;

require __DIR__ . '/vendor/autoload.php'; // Composer autoload 파일

// SFTP 연결 정보
$sftpHost = _HOST_;
$sftpPort = 22;
$sftpUsername = SFTP_NAME;
$pemFilePath = '/home/ubuntu/FirstTestKey.pem';

// SFTP 객체 생성
$sftp = new SFTP($sftpHost, $sftpPort);

// RSA 객체 생성 및 키 로드
$key = new RSA();
$key->load(file_get_contents($pemFilePath));

// SFTP 연결
if (!$sftp->login($sftpUsername, $Key)) {
    exit('Login Failed');
}

// 업로드
$localFile = 'C:/Users/chomk/Downloads/수정사항.png';
$remoteFile = '/home/ubuntu/수정사항.png';

if ($sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE)) {
    echo "File uploaded successfully.\n";
} else {
    echo "File upload failed.\n";
}

// 다운로드
$localDownloadFile = 'C:/Users/chomk/Downloads/수정사항.png';
$remoteDownloadFile = '/home/ubuntu/수정사항.png';

if ($sftp->get($remoteDownloadFile, $localDownloadFile)) {
    echo "File downloaded successfully.\n";
} else {
    echo "File download failed.\n";
}

// SFTP 연결 닫기
$sftp->disconnect();

?>