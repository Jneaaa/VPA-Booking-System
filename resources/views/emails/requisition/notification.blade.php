@component('mail::message')
# Requisition Update

Hello {{ $form->requester_name }},

{{ $messageText }}

**Requisition ID:** {{ $form->id }}  
**Purpose:** {{ $form->purpose }}  
**Status:** {{ $form->status->name }}

@component('mail::button', ['url' => url('/requisition/'.$form->id)])
View Your Requisition
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
