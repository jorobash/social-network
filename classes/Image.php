<?php

class Image {
    public static function upload($formname,$query, $params){
        //    print_r($_FILES);
        $image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));
        $imageUrl = "https://api.imgur.com/3/image";

        $options = array('http' => array(
            'method' => "POST",
            'header' => "Authorization: Bearer b07faa675773b93871f142f65ef04504faa857a0\n".
                "Content-Type: application/x-www-form-urlencoded",
            'content' => $image
        ));
        $context = stream_context_create($options);

//    check size of the image
        if($_FILES[$formname]['size'] > 10240000){
            die('Image too big, must be 10MB or less');
        }
//    send the imgae and headers to the https://api.imgur.com/3/image
        $response = file_get_contents($imageUrl, false, $context);
        $response = json_decode($response);

        $preparams = array($formname => $response->data->link);

        $params = $preparams + $params;

        DB::query($query, $params);
    }
}