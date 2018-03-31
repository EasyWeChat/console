<?php

namespace EasyWeChat\Console\Commands\Menus;

use EasyWeChat\Console\Commands\Command;
use EasyWeChat\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListMenus
 *
 * @author froger_me <alex@froger.me>
 */
class ListMenus extends Command
{
    protected $name = 'menus:list_menus';
    protected $description = 'List Official Account menus (on screen or in a file)';
    protected $help = 'This command allows you to list Wechat Official Account\'s menus in a JSON structure.';
    protected $options = [
        ['app_id', null, InputOption::VALUE_OPTIONAL, 'App ID of the Official Account.'],
        ['secret', null, InputOption::VALUE_OPTIONAL, 'The secret key of the Official Account'],
        ['token', null, InputOption::VALUE_OPTIONAL, 'The token of the Official Account'],
        ['aes_key', null, InputOption::VALUE_OPTIONAL, 'The AES key of the Official Account'],
        ['file_output', null, InputOption::VALUE_OPTIONAL, 'The file where to save the JSON structure of menus (optional).'],
    ];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appId = $this->option('app_id');
        $secret = $this->option('secret');
        $token = $this->option('token');
        $aesKey = $this->option('aes_key');
        $jsonPath = $this->option('file_output');

        if (empty($appId)) {
            $appId = $this->ask('appId');
        }

        if (empty($secret)) {
            $secret = $this->ask('secret');
        }

        if (empty($token)) {
            $token = $this->ask('token');
        }

        if (empty($aesKey)) {
            $aesKey = $this->ask('aes_key');
        }

        $app = Factory::officialAccount([
            'app_id' => $appId,
            'secret' => $secret,
            'token' => $token,
            'aes_key' => $aesKey,
        ]);

        $list = $app->menu->list();

        if (isset($list['errcode']) && 46003 === $list['errcode']) {
            $this->line('No menu found');
            return;
        }

        if (!empty($jsonPath)) {
            try {
                if (file_exists($jsonPath) && !is_writable($jsonPath)) {
                    throw new \Exception('The provided path is not a writable file.');
                }
                $fp = fopen($jsonPath, 'w');
                fwrite($fp, json_encode($list, JSON_PRETTY_PRINT));
                fclose($fp);
            } catch (\Exception $e) {
                $this->line('Error while outputing result to ' . $jsonPath);
                $this->line($e->getMessage());
                $this->line('');
                $this->line(json_encode($list, JSON_PRETTY_PRINT));
            }
        } else {
            $this->line(json_encode($list, JSON_PRETTY_PRINT));
        }
    }
}