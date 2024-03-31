<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Carbon\Carbon;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [$this, 'timeAgoFilter']),
        ];
    }

    public function timeAgoFilter($datetime): string
    {
        $carbon = Carbon::parse($datetime);
        return $carbon->diffForHumans();
    }
}