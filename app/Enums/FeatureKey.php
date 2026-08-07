<?php

namespace App\Enums;

enum FeatureKey: string
{
    case Products = 'products';
    case Orders = 'orders';
    case Inventory = 'inventory';
    case Notifications = 'notifications';
}
