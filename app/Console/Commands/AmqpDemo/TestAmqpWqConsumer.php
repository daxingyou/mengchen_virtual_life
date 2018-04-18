<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpWqConsumer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-wq-consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp queue worker test consumer';

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
        $channel->queue_declare('hello', false, true, false, false);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $channel->basic_qos(null, 1, null);     //告知broker上一个消息未确认之前，只push一个消息过来
        $channel->basic_consume('hello', '', false, false, false, false, [$this, 'msgCallback']);
        //第四个参数，关闭no_ack，关闭自动确认

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function msgCallback(AMQPMessage $msg)
    {
        echo " [x] Received ", $msg->body, "\n";
        sleep(substr_count($msg->body, '.'));
        echo " [x] Done", "\n";
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
}
