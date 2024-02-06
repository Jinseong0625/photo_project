<?php

include __DIR__ . "/phpqrcode/qrlib.php";

ob_start("colback");

$codeText = "반갑습니다";

$debugLog = ob_get_contents();

ob_end_clean();

QRcode::png($codeText);

?>