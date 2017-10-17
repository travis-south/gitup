<?php

namespace TravisSouth\Gitup\Console;

use Whoops\Run;
use Whoops\Handler\PlainTextHandler;

final class ErrorHandler
{

    const TIMEOUT = 2;

    private $endpoint;

    private $requests = [];

    public function setUp()
    {
        $errorHandler = new Run;
        $errorHandler->pushHandler(new PlainTextHandler);
        $errorHandler->register();
    }
}
