<?php
// src/Enum/GroupeSanguin.php

namespace App\Enum;

enum GroupeSanguin: string
{
    case APositif = 'A+';
    case ANegatif = 'A-';
    case BPositif = 'B+';
    case BNegatif = 'B-';
    case ABPositif = 'AB+';
    case ABNegatif = 'AB-';
    case OPositif = 'O+';
    case ONegatif = 'O-';
}