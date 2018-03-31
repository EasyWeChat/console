<?php

namespace EasyWeChat\Console\Commands\Menus;

use EasyWeChat\Console\Commands\Command;
use EasyWeChat\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateMenus
 *
 * @author froger_me <alex@froger.me>
 */
class CreateMenus extends Command
{
    protected $name = 'menus:create_menus';
    protected $description = 'Create Official Account menus (all users)';
    protected $help = 'This command allows you to set a Wechat Official Account\'s menus using a file containing a JSON structure.';
    protected $options = [
        ['app_id', null, InputOption::VALUE_OPTIONAL, 'App ID of the Official Account.'],
        ['secret', null, InputOption::VALUE_OPTIONAL, 'The secret key of the Official Account'],
        ['token', null, InputOption::VALUE_OPTIONAL, 'The token of the Official Account'],
        ['aes_key', null, InputOption::VALUE_OPTIONAL, 'The AES key of the Official Account'],
        ['file', null, InputOption::VALUE_OPTIONAL, 'The file with the JSON structure of menus to create.'],
    ];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appId = $this->option('app_id');
        $secret = $this->option('secret');
        $token = $this->option('token');
        $aesKey = $this->option('aes_key');
        $jsonPath = $this->option('file');

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

        if (empty($jsonPath)) {
            $jsonPath = $this->ask('file');
        }

        $app = Factory::officialAccount([
            'app_id' => $appId,
            'secret' => $secret,
            'token' => $token,
            'aes_key' => $aesKey,
        ]);

        try {
            $content = file_get_contents($jsonPath);

            if (false === $content) {
                throw new \Exception("Cannot read file content.");
            } 

            $menusStructure = json_decode($content, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                $message = '';

                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $message = 'Maximum stack depth exceeded';
                    break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $message = 'Underflow or the modes mismatch';
                    break;
                    case JSON_ERROR_CTRL_CHAR:
                        $message = 'Unexpected control character found';
                    break;
                    case JSON_ERROR_SYNTAX:
                        $message = 'Syntax error, malformed JSON';
                    break;
                    case JSON_ERROR_UTF8:
                        $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                    default:
                        $message = 'Unknown error';
                    break;
                }

                throw new \Exception("JSON error: " . $message);
            }

            if (isset($menusStructure['menu']) && isset($menusStructure['menu']['button']) && is_array($menusStructure['menu']['button'])) {
                $buttons = array();

                foreach ($menusStructure['menu']['button'] as $key => $buttonStructure) {
                    $button = array();

                    foreach ($buttonStructure as $buttonStructureKey => $value) {
                        if (!($buttonStructureKey === 'sub_button' && empty($value))) {
                            $button[$buttonStructureKey] = $value;
                        }
                    }
                    $buttons[] = $button;
                }
                $result = $app->menu->create($buttons);

                if ($output->isDebug()) {
                    $this->line(print_r($buttons, true));
                }

                if ($output->isVerbose() || $output->isVeryVerbose() || $output->isDebug()) {
                    $this->line(print_r($result, true));
                }
            }

        } catch (\Exception $e) {
            $this->line('Error while attempting to create menus using ' . $jsonPath);
            $this->line($e->getMessage());
        }
    }
}