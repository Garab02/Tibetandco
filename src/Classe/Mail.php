<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    private $api_key = '4a04ec3e9f1095f34d2447824031e050';
    private $api_key_secret = '59c857f9317c9a0f550aa24ebbebeb3b';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
       
      $body = [
          'Messages' => [
        [
            'From' => [
                'Email' => "tenzonetech@gmail.com",
                'Name' => "tibetandco"
            ],
            'To' => [
                [
                    'Email' => "$to_email",
                    'Name' => "$to_name"
                ]
            ],
            'TemplateID' => 5134637,
            'TemplateLanguage' => true,
            'Subject' => "$subject",
            'Variables' => [
                'content' => $content
            ]
        ]
    ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
        }
}