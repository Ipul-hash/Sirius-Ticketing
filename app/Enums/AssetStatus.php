<?php

namespace App\Enums;

enum AssetStatus: string
{
    case InUse = 'in_use';
    case Available = 'available';
    case Maintenance = 'maintenance';
    case Retired = 'retired';
}
