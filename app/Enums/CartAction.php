<?php

namespace App\Enums;

enum CartAction: string
{
    case INCREASE = 'increase';
    case DECREASE = 'decrease';
    case REMOVE = 'remove';
    case ADD_AMMO = 'add_ammo';
    case REMOVE_AMMO = 'remove_ammo';
    case CHANGE_AMMO = 'change_ammo';
}
