<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpRoutingProducer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-routing-producer {routing=info} {msg=hello...}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp routing test producer';

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
        $exName = 'ex-demo-routing';
        $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $channel = $connection->channel();
        $channel->exchange_declare($exName, 'direct', false, false, false);

        $msg = new AMQPMessage($msgData, [
            //'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,   //消息持久化
        ]);

        $channel->basic_publish($msg, $exName, $routingKey);    //将消息发送到指定的routing key上

        $this->logInfo('Sent msg');
        $channel->close();
        $connection->close();
    }
}
