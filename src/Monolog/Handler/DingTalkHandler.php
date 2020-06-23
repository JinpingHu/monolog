<?php declare(strict_types=1);

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Utils;

class DingTalkHandler extends AbstractProcessingHandler
{
    private $msgPerfix;
    private $accessToken;

    public function __construct(string $accessToken, string $msgPerfix, $level = Logger::ERROR, bool $bubble = true)
    {
        $this->accessToken = $accessToken;
        $this->msgPerfix = $msgPerfix;

        parent::__construct($level, $bubble);
    }

    public function write(array $record): void
    {
        $postData = [
            "value1" => $record["channel"],
            "value2" => $record["level_name"],
            "value3" => $this->msgPerfix.$record["message"],
        ];
        $postString = Utils::jsonEncode($postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oapi.dingtalk.com/robot/send?access_token='.$this->accessToken);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);

        Curl\Util::execute($ch);
    }
}
