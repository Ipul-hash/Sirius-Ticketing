<?php

namespace App\Enums;

enum CompanyPlan: string
{
    case Starter = 'starter';
    case Professional = 'professional';
    case Enterprise = 'enterprise';
}
