<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpTopicProducer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-topic-producer {routing} {msg=hello...}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp topic test producer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $msgData = $this->argument('msg');
        $routingKey = $this->argument('routing');

        $rabbitmqHost = env('RABBITMQ_HOST');
        $rabbitmqPort = env('RABBITMQ_PORT');
        $rabbitmqUser = 'admin';
        $rabbitmqPass = 'password';
        $exName = 'ex.topic.demo';
        $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $channel = $connection->channel();
        $channel->exchange_declare($exName, 'topic', false, false, false);

        $msg = new AMQPMessage($msgData, [
            //'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,   //消息持久化
        ]);

        $channel->basic_publish($msg, $exName, $routingKey);

        $this->logInfo('Sent msg');
        $channel->close();
        $connection->close();
    }
}
