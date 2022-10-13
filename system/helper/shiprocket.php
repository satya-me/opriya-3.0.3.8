<?php
include DIR_APPLICATION . 'model/setting/' . 'extension' . '.php';


function ShipLoginAuth($data)
{

    // return $data['email'];
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "email": "'.$data['email'].'",
            "password": "'.$data['password'].'"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            // 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMwMDU2ODYsImlzcyI6Imh0dHBzOi8vYXBpdjIuc2hpcHJvY2tldC5pbi92MS9leHRlcm5hbC9hdXRoL2xvZ2luIiwiaWF0IjoxNjY1MTUzMDQxLCJleHAiOjE2NjYwMTcwNDEsIm5iZiI6MTY2NTE1MzA0MSwianRpIjoiZmFqTUNPZTZLTzllTUpVNCJ9.k_arYVOEMIuBAHNoz1A47v6IRCCdPT1gQh2Wb8B8hZ8'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

function call_satya($arg)
{
    return $arg;
}
