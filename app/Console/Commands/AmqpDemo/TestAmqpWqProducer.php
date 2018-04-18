<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpWqProducer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-wq-producer {msg=hello...}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp queue worker test producer';

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

        $rabbitmqHost = env('RABBITMQ_HOST');
        $rabbitmqPort = env('RABBITMQ_PORT');
        $rabbitmqUser = 'admin';
        $rabbitmqPass = 'password';
        $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, true, false, false);    //队列持久化

        $msg = new AMQPMessage($msgData, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,   //消息持久化
        ]);

        $channel->basic_publish($msg, '', 'hello');

        $this->logInfo('Sent msg');
        $channel->close();
        $connection->close();
    }
}
