<?php


namespace DassumanLaravelTickets\Commands;

use Illuminate\Console\Command;
use DassumanLaravelTickets\Models\Ticket;

/**
 * Class AutoCloseCommand
 *
 * The command checks if a ticket is unanswered a specific time,
 * that is in the configuration defined and then close it
 *
 * @package DassumanLaravelTickets\Commands
 */
class AutoCloseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:autoclose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close any ticket that has become inactive.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Ticket::query()->state([ 'OPEN', 'ANSWERED' ])->where(
            'updated_at',
            '<',
            now()->subDays(config('laravel-tickets.autoclose-days'))
        )->each(function (Ticket $ticket) {
            $ticket->update([ 'state' => 'CLOSED' ]);
        });
    }
}
