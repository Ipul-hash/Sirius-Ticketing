<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case PendingApproval = 'pending_approval';
    case InProgress = 'in_progress';
    case PendingUser = 'pending_user';
    case Resolved = 'resolved';
    case Closed = 'closed';
}
