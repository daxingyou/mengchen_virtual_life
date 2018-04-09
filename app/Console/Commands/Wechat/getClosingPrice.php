<?php

namespace App\Console\Commands\Wechat;

use App\Console\BaseCommand;
use App\Models\StockClosingPrice;
use App\Models\StockHolders;
use App\Models\StockTradingHistory;
use Carbon\Carbon;

class getClosingPrice extends BaseCommand
{
    protected $initDate = '2018-04-01';     //初始日期

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wechat:closing-price {date=today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算所有股票的每日收盘价并入库';

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
        $date = Carbon::parse($this->argument('date'))->toDateString();
        $this->validateDate($date);     //检查时间参数，不能是未来的时间
        StockHolders::all()
            ->groupBy('stock_code')
            ->keys()
            ->each(function ($stockCode) use ($date) {
                $tradingHistory = StockTradingHistory::where('stock_code', $stockCode)
                    ->whereDate('created_at', $date)
                    ->orderBy('id', 'desc')
                    ->first();

                //如果单日无交易，取上一日的收盘价为今日收盘价
                if (empty($tradingHistory)) {
                    $this->logInfo("股票：${stockCode} 在 ${date} 当日无成交，获取昨日收盘价.");
                    $yesterday = Carbon::parse($date)->subDay(1)->toDateString();
                    $closingPrice = $this->getYesterdayClosingPrice($stockCode, $yesterday);
                } else {
                    $closingPrice = $tradingHistory->price;
                }

                StockClosingPrice::firstOrCreate([
                    'stock_code' => $stockCode,
                    'date' => $date,
                ], ['closing_price' => $closingPrice]);

                $this->logInfo("股票: ${stockCode}, ${date} , 收盘价: ${closingPrice}, 入库成功");
            });
    }

    protected function validateDate($date)
    {
        if (Carbon::parse($date)->isFuture()) {
            $this->logError("日期${date}，不能是未来的时间");
            exit();
        }
    }

    protected function getYesterdayClosingPrice($stockCode, $yesterday)
    {
        $yesterdayStockPrice = StockClosingPrice::where('stock_code', $stockCode)
            ->whereDate('date', $yesterday)
            ->first();
        if (!empty($yesterdayStockPrice)) {
            return $yesterdayStockPrice->closing_price;
        } else {
            $yesterday = Carbon::parse($yesterday)->subDay(1)->toDateString();
            if (Carbon::parse($yesterday)->lt(Carbon::parse($this->initDate))) {
                return 0;   //如果在初始日期之前此股票都没成交过，那么收盘价为0;
            } else {
                return $this->getYesterdayClosingPrice($yesterday);
            }
        }
    }
}
