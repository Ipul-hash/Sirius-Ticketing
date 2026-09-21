<?php

namespace App\Enums;

enum TicketApprovalStatus: string
{
    case None = 'none';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
