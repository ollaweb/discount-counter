<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ShipOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ship-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда раз в 30 минут меняет все статусы заказов с accepted на shipping';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Order::where('status', 'accepted')->update(['status' => 'shipping']);
        $this->line("Отправили подтвержденные заказы");
        return Command::SUCCESS;
    }
}
