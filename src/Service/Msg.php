<?php

namespace App\Service;


class Msg
{
    public function Message($username)
    {

        $messages = "bonjour" . $username . ",Nous avons beaucoup de nouveater a vous présenter!";


        return $messages;
    }
}
