<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpTopicConsumer extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-topic-consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp topic test consumer';

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
        $exName = 'ex-demo-ps';
        $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $channel = $connection->channel();
        $channel->exchange_declare($exName, 'fanout', false, false, false);
        list($queueName, ,) = $channel->queue_declare("", false, false, true, false);   //声明一个随机名字的自动销毁的队列
        $channel->queue_bind($queueName, $exName);  //fanout交换机忽略route key
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        //$channel->basic_qos(null, 1, null);     //告知broker上一个消息未确认之前，只push一个消息过来
        $channel->basic_consume($queueName, '', false, false, false, false, [$this, 'msgCallback']);
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
