<?php
set_time_limit(0);
//ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
if (!file_exists('jdf.php')) {
    copy('http://us.cdn.persiangig.com/preview/NqVx1LPkXc/jdf.php', 'jdf.zip');
}
if (!file_exists("bg.jpg")) {
    copy("http://us.cdn.persiangig.com/preview/mbQwryDJFP/medium/bg.jpg", "bg.jpg");
}
if (!file_exists("iranian_sans.ttf")) {
    copy("http://us.cdn.persiangig.com/preview/ggkbw9GX4e/iranian_sans.ttf", "iranian_sans.ttf");
}
if (file_exists('MadelineProto.log')) {
    unlink('MadelineProto.log');
}
include 'madeline.php';
include 'jdf.php';

$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {

    $MadelineProto->start();

    $Year = 1399;
    $New_Year = jmktime(7, 19, 37, 1, 1, $Year);
    $Current_Time = time();
    $Passive = false;

    if ($New_Year >= $Current_Time) {
        $Discord = $New_Year - $Current_Time;
    } else {
        $Discord = $Current_Time - $New_Year;
        $Passive = true;
    }

    $Discord_Day = floor($Discord / 86400);
    $Discord_Hour = floor(($Discord / 3600) % 24);
    $Discord_Min = floor(($Discord / 60) % 60);
    //$Discord_Sec = floor($Discord % 60);

    try {
        if ($Discord_Min == 0) {
            yield $MadelineProto->account->updateProfile(['about' => "💎 سال نو مبارک...💎"]);
        } else {
            if ($Passive) {
                yield $MadelineProto->account->updateProfile(['about' => "⏰ تاکنون $Discord_Day روز $Discord_Hour ساعت و $Discord_Min دقیقه در سال $Year زنده موندیم 💎"]);
            } else {
                yield $MadelineProto->account->updateProfile(['about' => "⏰ فقط $Discord_Day روز $Discord_Hour ساعت و $Discord_Min دقیقه باقی مانده تا آغاز سال $Year 💎"]);
            }
        }
    } catch (\danog\MadelineProto\RPCErrorException $e) {
        $MadelineProto->logger($e);
        echo $e;
    }


    $image = imagecreatefromjpeg('bg.jpg');
    $white = imagecolorallocate($image, 255, 255, 255);
    $font = "iranian_sans.ttf";
    $txt = jdate('H:i');
    imagettftext($image, 50, 0, 320, 160, $white, $font, $txt);
    $txt = jdate('Y/m/d');
    imagettftext($image, 50, 0, 230, 250, $white, $font, $txt);
    $txt = date('Y/m/d');
    imagettftext($image, 50, 0, 230, 350, $white, $font, $txt);

    imagejpeg($image, "time.jpg");

    try {
        $photo = yield $MadelineProto->photos->getUserPhotos(['user_id' => 'me', 'offset' => 0, 'max_id' => 0, 'limit' => 1]);
        $inputPhoto = ['_' => "inputPhoto", 'id' => $photo["photos"]["0"]["id"], 'access_hash' => $photo["photos"]["0"]["access_hash"], 'file_reference' => "bytes"];
        yield $MadelineProto->photos->deletePhotos(['id' => [$inputPhoto]]);
        $MessageMedia = yield $MadelineProto->messages->uploadMedia(['peer' => 'me', 'media' => ['_' => 'inputMediaUploadedPhoto', 'file' => 'time.jpg']]);
        yield  $MadelineProto->photos->updateProfilePhoto(['id' => $MessageMedia]);
        echo "OK :).";
    } catch (\danog\MadelineProto\RPCErrorException $e) {
        $MadelineProto->logger($e);
        echo $e;
    }
});
