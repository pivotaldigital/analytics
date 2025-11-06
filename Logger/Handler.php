<?php

namespace Pivotal\Analytics\Logger;

use Monolog\Level;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Level::Info;

    /**
     * Log file name
     * @var string
     */
    protected $fileName = '/var/log/pivotal/analytics.log';
}
