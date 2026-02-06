<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';
    case PREFER_NOT_TO_SAY = 'Prefer not to say';
}
