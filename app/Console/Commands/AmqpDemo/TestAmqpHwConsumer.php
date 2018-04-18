<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpHwConsumer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-hw-consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp consumer test hello world';

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
        $rabbitmqHost = env('RABBITMQ_HOST');
        $rabbitmqPort = env('RABBITMQ_PORT');
        $rabbitmqUser = 'admin';
        $rabbitmqPass = 'password';
        $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $channel = $connection->channel();
        $channel->queue_declare('hello');
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $channel->basic_consume('hello', '', false, true, false, false, [$this, 'msgCallback']);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function msgCallback(AMQPMessage $msg)
    {
        echo " [x] Received ", $msg->body, "\n";
    }
}
