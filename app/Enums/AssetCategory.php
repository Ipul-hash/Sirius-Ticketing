<?php

namespace App\Enums;

enum AssetCategory: string
{
    case Hardware = 'hardware';
    case Server = 'server';
    case Network = 'network';
    case SoftwareLicense = 'software_license';
}
