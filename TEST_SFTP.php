<?php

use phpseclib\Net\SFTP;

require __DIR__ . '/vendor/autoload.php'; // Composer autoload 파일

// SFTP 연결 정보
$sftpHost = _HOST_;
$sftpPort = 22;
$sftpUsername = SFTP_NAME;
$pemFilePath = '/home/ubuntu/FirstTestKey.pem';

// SFTP 객체 생성
$sftp = new SFTP($sftpHost, $sftpPort);

$key = new \phpseclib\Crypt\RSA();
$key->loadKey(file_get_contents($pemFilePath));

// SFTP 연결
if (!$sftp->login($sftpUsername, $Key)) {
    exit('Login Failed');
}

// 업로드
$localFile = 'local_file_path.txt';
$remoteFile = 'remote_file_path.txt';

if ($sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE)) {
    echo "File uploaded successfully.\n";
} else {
    echo "File upload failed.\n";
}

// 다운로드
$localDownloadFile = 'local_download_file.txt';
$remoteDownloadFile = 'remote_download_file.txt';

if ($sftp->get($remoteDownloadFile, $localDownloadFile)) {
    echo "File downloaded successfully.\n";
} else {
    echo "File download failed.\n";
}

// SFTP 연결 닫기
$sftp->disconnect();

?>