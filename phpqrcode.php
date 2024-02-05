<?php

require 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// QR 코드 생성 옵션 설정
$options = new QROptions([
    'version'      => 5, // QR 코드 버전 (1~40)
    'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel'     => QRCode::ECC_L,
    'scale'        => 10,
]);

// QR 코드 생성
$qrcode = new QRCode($options);
$image = $qrcode->render('Hello, QR Code!');

// 생성된 이미지 출력
header('Content-Type: image/png');
echo $image;

?>
