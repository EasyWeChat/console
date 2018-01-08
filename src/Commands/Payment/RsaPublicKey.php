<?php

namespace EasyWeChat\Console\Commands\Payment;

use EasyWeChat\Console\Commands\Command;
use EasyWeChat\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class RsaPublicKey
 *
 * @author overtrue <i@overtrue.me>
 */
class RsaPublicKey extends Command
{
    protected $name = 'payment:rsa_public_key';
    protected $description = 'Get RSA public key';
    protected $help = 'This command allows you to get rsa public key content, and store as a file, or transform PKCS#1 to PKCS#8.';
    protected $options = [
        ['mch_id', null, InputOption::VALUE_OPTIONAL, 'Merchant Id of Wechat Pay.'],
        ['save_path', null, InputOption::VALUE_OPTIONAL, 'Output path of public.pem'],
        ['format', null, InputOption::VALUE_OPTIONAL, 'PKCS#8 or PKCS#1', 'PKCS#8'],
        ['api_key', null, InputOption::VALUE_OPTIONAL, 'The API key which download from wechat payment dashboard.'],
        ['cert_path', null, InputOption::VALUE_OPTIONAL, 'The API cert file path.'],
        ['key_path', null, InputOption::VALUE_OPTIONAL, 'The API key file path.'],
    ];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mchId = $this->option('mch_id');
        $savePath = $this->option('save_path');
        $format = $this->option('format');
        $key = $this->option('api_key');
        $certPath = $this->option('cert_path');
        $keyPath = $this->option('key_path');

        if (empty($mchId)) {
            $mchId = $this->ask('mch_id');
        }

        if (empty($savePath)) {
            $savePath = \sprintf('./public-%s.pem', $mchId);
        }

        if (empty($key)) {
            $key = $this->ask('API Key');
        }

        if (empty($certPath)) {
            $certPath = $this->ask('API Cert file Path');
        }

        if (empty($keyPath)) {
            $keyPath = $this->ask('API key file Path');
        }

        $app = Factory::payment([
            'mch_id' => $mchId,
            'key' => $key,
            'cert_path' => $certPath,
            'key_path' => $keyPath,
        ]);

        $result = $app->security->getPublicKey();

        if (empty($result['pub_key'])) {
            return $this->error('Get public key fail:'.\GuzzleHttp\json_encode($result));
        }

        \file_put_contents($savePath, $result['pub_key']);

        if (false !== \stripos($format, '8')) {
            $cmd = new Process(\sprintf('openssl rsa -RSAPublicKey_in -in %s -out %s', $savePath, $savePath));
            $code = $cmd->run();
        }

        $this->line(\sprintf('Public key of mch_id:%s saved as %s', $mchId, $savePath));
    }
}