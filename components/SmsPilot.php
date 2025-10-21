<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;

class SmsPilot extends Component
{
    /**
     * @var string API key from smspilot.ru
     */
    public $apiKey;

    /**
     * @var string API URL
     */
    public $apiUrl = 'https://smspilot.ru/api.php';

    /**
     * @var string Sender name
     */
    public $sender = 'INFORM';

    /**
     * Send SMS
     *
     * @param string $phone Phone number
     * @param string $message Message text
     * @return bool
     */
    public function send($phone, $message)
    {
        if (empty($this->apiKey)) {
            Yii::error('SMS Pilot API key is not set');
            return false;
        }

        try {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($this->apiUrl)
                ->setData([
                    'send' => $message,
                    'to' => $this->formatPhone($phone),
                    'from' => $this->sender,
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ])
                ->send();

            if ($response->isOk) {
                $data = $response->data;
                
                if (isset($data['error'])) {
                    Yii::error('SMS Pilot error: ' . $data['error']['description']);
                    return false;
                }

                if (isset($data['send'])) {
                    Yii::info('SMS sent successfully to ' . $phone . ': ' . $message);
                    return true;
                }
            }

            Yii::error('SMS Pilot response error: ' . $response->content);
            return false;

        } catch (\Exception $e) {
            Yii::error('SMS Pilot exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification about new book
     *
     * @param string $phone
     * @param string $bookTitle
     * @param string $authorName
     * @return bool
     */
    public function sendNewBookNotification($phone, $bookTitle, $authorName)
    {
        $message = "Новая книга автора {$authorName}: \"{$bookTitle}\". Подробнее на сайте.";
        return $this->send($phone, $message);
    }

    /**
     * Format phone number
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhone($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // If starts with 8, replace with 7
        if (substr($phone, 0, 1) === '8') {
            $phone = '7' . substr($phone, 1);
        }
        
        // Add + if not present
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * Check balance (for real API key)
     *
     * @return float|false
     */
    public function getBalance()
    {
        if (empty($this->apiKey)) {
            return false;
        }

        try {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($this->apiUrl)
                ->setData([
                    'balance' => 'get',
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ])
                ->send();

            if ($response->isOk) {
                $data = $response->data;
                return isset($data['balance']) ? (float)$data['balance'] : false;
            }

            return false;
        } catch (\Exception $e) {
            Yii::error('SMS Pilot balance check exception: ' . $e->getMessage());
            return false;
        }
    }
}

