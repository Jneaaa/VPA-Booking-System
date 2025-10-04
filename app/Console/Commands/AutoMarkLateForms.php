<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AdminApprovalController;

class AutoMarkLateForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:mark-late';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark requisition forms as late if they are past their end datetime + 4 hours grace period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic late form detection...');
        
        $controller = new AdminApprovalController();
        $result = $controller->autoMarkLateForms();
        
        if (isset($result->getData()->error)) {
            $this->error('Error: ' . $result->getData()->error);
            return 1;
        }
        
        $data = $result->getData();
        $this->info("Automatic late detection completed successfully!");
        $this->info("Processed: {$data->processed} forms");
        $this->info("Marked as late: {$data->marked_late} forms");
        
        return 0;
    }
}