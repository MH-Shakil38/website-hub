<?php

namespace Modules\EventManagement\App\Models;

class EmTicketBooking extends EventManagementBaseModel
{
    protected $table = 'em_ticket_bookings';

    protected $fillable = [
        'ticket_code',
        'attendee_name',
        'email',
        'phone',
        'company',
        'plan_name',
        'unit_price',
        'quantity',
        'total_amount',
        'session_choice',
        'status',
    ];
}
