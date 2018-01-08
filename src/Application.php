<?php


namespace EasyWeChat\Console;


/**
 * Class Application
 *
 * @author overtrue <i@overtrue.me>
 */
class Application extends \Symfony\Component\Console\Application
{
    const NAME = 'EasWeChat Console Tool';
    const VERSION = '1.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
    }
}