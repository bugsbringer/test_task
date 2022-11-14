<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("календарь дел");

$APPLICATION->IncludeComponent(
    'custom:todocalendar',
    '',
);
 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
