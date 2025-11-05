<?php

namespace Pivotal\Analytics\Logger;

use Pivotal\Logger\Logger\Logger as PivotalLogger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = PivotalLogger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/pivotal/analytics.log';
}