<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('unique', [$this, 'uniqueByKey']),
        ];
    }

    /**
     * Retire les doublons d’un tableau d’objets/tableaux selon une clé.
     * Ex : teams|unique('id')
     */
    public function uniqueByKey(iterable $items, string $key): array
    {
        $seen = [];
        $out  = [];

        foreach ($items as $item) {
            $value = is_object($item) ? $item->{'get'.ucfirst($key)}() : $item[$key];
            if (!in_array($value, $seen, true)) {
                $seen[] = $value;
                $out[]  = $item;
            }
        }
        return $out;
    }
}