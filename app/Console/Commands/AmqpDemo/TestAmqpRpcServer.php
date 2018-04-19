<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use App\Exceptions\MqException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestAmqpRpcServer extends BaseCommand
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection;
     */
    protected $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel;

    protected $rpcQueueName = 'q.rpc';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amqp-rpc-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'amqp rpc test server';

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

        //声明rcp queue
        $this->channel->queue_declare($this->rpcQueueName, false, false, false, false);

        echo " [x] Awaiting RPC requests\n";

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->rpcQueueName, '', false, false, false, false, [$this, 'callback']);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->closeAmqp();
    }

    protected function fib($n)
    {
        if ($n === 0)
            return 0;
        if ($n === 1)
            return 1;
        return $this->fib($n-1) + $this->fib($n-2);
    }

    //调用rpc server方法
    protected function callFunc($func, ...$params)
    {
        try {
            $data = call_user_func($func, ...$params);
            $result = $this->buildResult($data);
        } catch (\Exception $exception) {
            $result = $this->buildResult($exception->getMessage(), $exception->getCode(), false);
        }
        return $result;
    }

    public function callback($req)
    {
        $n = intval($req->body);

        echo " [.] fib(", $n, ")\n";

        $result = $this->callFunc([$this, 'fib'], $n);

        $msg = new AMQPMessage($result, [
            'correlation_id' => $req->get('correlation_id'),
            'content_type' => 'application/json',
        ]);

        $req->delivery_info['channel']->basic_publish(
            $msg, '', $req->get('reply_to'));
        $this->ack($req);
    }

    protected function buildResult($res, $code = -1, $success = true)
    {
        return json_encode([
            'success' => $success,
            'code' => $code,
            'data' => $res,
        ]);
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
