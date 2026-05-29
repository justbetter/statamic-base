<?php

namespace JustBetter\StatamicBase\Enums;

enum UpdateStatus: string
{
    case UpToDate = 'up_to_date';
    case Patch = 'patch';
    case Minor = 'minor';
    case Major = 'major';
    case Unknown = 'unknown';
}
