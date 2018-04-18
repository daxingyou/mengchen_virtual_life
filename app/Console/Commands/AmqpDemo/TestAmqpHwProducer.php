<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpHwProducer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "test:amqp-hw-producer {msg=hello world}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp producer test hello world';

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
        $msg = $this->argument('msg');

        $rabbitmqHost = env('RABBITMQ_HOST');
        $rabbitmqPort = env('RABBITMQ_PORT');
        $rabbitmqUser = 'admin';
        $rabbitmqPass = 'password';
        $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, true, false, false);
        $msg = new AMQPMessage($msg);
        $channel->basic_publish($msg, '', 'hello');
        $this->logInfo('Sent msg');
        $channel->close();
        $connection->close();
    }
}
