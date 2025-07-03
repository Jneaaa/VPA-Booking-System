<?php

namespace App\Jobs;

use App\Models\RequisitionForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRequisitionReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $requisition;
    public $reminderType;

    public function __construct(RequisitionForm $requisition, string $reminderType)
    {
        $this->requisition = $requisition;
        $this->reminderType = $reminderType;
    }

    public function handle()
    {
        $user = $this->requisition->user;
        $emailData = [
            'user' => $user,
            'requisition' => $this->requisition,
            'reminderType' => $this->reminderType
        ];

        $subject = '';
        $template = '';

        switch ($this->reminderType) {
            case 'pre_event':
                $subject = 'Reminder: Upcoming Reservation';
                $template = 'emails.requisition_pre_event_reminder';
                break;
            case 'pre_return':
                $subject = 'Reminder: Equipment Due Soon';
                $template = 'emails.requisition_pre_return_reminder';
                break;
            case 'return_due':
                $subject = 'Reminder: Equipment Due Today';
                $template = 'emails.requisition_return_due_reminder';
                break;
        }

        Mail::send($template, $emailData, function ($message) use ($user, $subject) {
            $message->to($user->email)
                    ->subject($subject);
        });
    }
}