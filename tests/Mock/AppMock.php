<?php

namespace LaPosteTest\Mock;

class AppMock extends \LaPoste\Suivi\App
{

    protected $code_to_return = 200;

    public function setCodeToReturn($code)
    {
        $this->code_to_return = $code;
    }

    public function callMultiple($requests)
    {
        $results = [];
        $results[] = file_get_contents(sprintf(
            '%s/%s.json',
            __DIR__,
            $this->code_to_return
        ));

        return $this->prepareResponse($results);
    }
}
