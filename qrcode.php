<?php
// phpqrcode 라이브러리를 사용하여 QR 코드를 생성하는 코드
include __DIR__ . "/phpqrcode/qrlib.php";

// 가게의 키값을 어딘가에서 가져오거나 설정
$storeId = 123; // 가게의 식별자, 실제로는 해당 가게의 고유한 키값이어야 합니다.

// QR 코드에 가게의 키값을 담아서 생성
$codeText = "http://34.64.137.179/menu/{$storeId}";

// QR 코드를 생성하고 출력
QRcode::png($codeText);
?>