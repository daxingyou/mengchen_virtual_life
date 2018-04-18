<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpRpcClient extends BaseCommand
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection;
     */
    protected $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel;

    /**
     * @var string
     */
    protected $corrId;

    /**
     * 服务发回来的响应
     * @var
     */
    protected $response;

    /**
     * @var string
     */
    protected $callBackQueueName;

    protected $rpcQueueName = 'q.rpc';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-rpc-client {n}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp rpc test client';

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
        $this->connectAmqp();

        //声明callback队列(exclusive置为true，只有此连接才能使用此queue，且连接断开之后自动删除之)
        list($this->callBackQueueName, ,) = $this->channel->queue_declare("", false, false, true, false);

        //监听响应队列
        $this->channel->basic_consume($this->callBackQueueName, '', false, false, false, false, [$this, 'onResponse']);

        //rcp调用
        $n = $this->argument('n');
        $this->callRpc($n);
        echo " [.] Got ", $this->response, "\n";

        $this->closeAmqp();
    }

    //请求rpc服务器
    protected function callRpc($n)
    {
        //每一次请求都清空上一次的res，生成新的id
        $this->response = null;
        $this->corrId = uniqid();

        $msg = new AMQPMessage((string) $n, [
            'correlation_id' => $this->corrId,
            'reply_to' => $this->callBackQueueName,
        ]);

        $this->channel->basic_publish($msg, '', $this->rpcQueueName);

        //如果没有收到响应就阻塞
        while (is_null($this->response)) {
            $this->channel->wait();
        }

        return intval($this->response);
    }

    public function onResponse($req)
    {
        if ((string)$req->get('correlation_id') === $this->corrId) {
            $this->response = $req->body;
            $this->ack($req);
        }
    }

    protected function connectAmqp()
    {
        $rabbitmqHost = env('RABBITMQ_HOST');
        $rabbitmqPort = env('RABBITMQ_PORT');
        $rabbitmqUser = 'admin';
        $rabbitmqPass = 'password';
        $this->connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPass);
        $this->channel = $this->connection->channel();
    }

    protected function ack($req)
    {
        $req->delivery_info['channel']->basic_ack(
            $req->delivery_info['delivery_tag']);
    }

    protected function closeAmqp()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
