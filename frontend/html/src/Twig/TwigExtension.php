<?php

namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('docker_date', [$this, 'date']),
            new TwigFilter('file_size', [$this, 'fileSize']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('docker_date', [$this, 'date']),
            new TwigFunction('file_size', [$this, 'fileSize']),
        ];
    }

    /**
     * @param mixed $datetime
     * @param int $parts
     * @return string
     */
    public function date($datetime, int $parts = 2): string
    {
        $carbon = is_numeric($datetime) ? Carbon::createFromFormat('U', $datetime) : new Carbon($datetime);
        return $carbon->diffForHumans(Carbon::now(), Carbon::DIFF_RELATIVE_TO_NOW, false, $parts, Carbon::JUST_NOW);
    }

    /**
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public function fileSize(int $bytes, int $precision = 2): string
    {
        $size = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor(log($bytes, 1024));
        return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . @$size[(int) $factor];
    }
}
