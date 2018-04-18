<?php

namespace App\Console\Commands\AmqpDemo;

use App\Console\BaseCommand;
use Illuminate\Console\Command;
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

    public function callback($req)
    {
        $n = intval($req->body);

        echo " [.] fib(", $n, ")\n";

        $msg = new AMQPMessage((string) $this->fib($n), [
            'correlation_id' => $req->get('correlation_id'),
        ]);

        $req->delivery_info['channel']->basic_publish(
            $msg, '', $req->get('reply_to'));
        $this->ack($req);
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
