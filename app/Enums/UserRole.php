<?php

namespace App\Enums;

enum UserRole: string
{
    case Superadmin = 'superadmin';
    case CompanyAdmin = 'company_admin';
    case Agent = 'agent';
    case Requester = 'requester';
}
