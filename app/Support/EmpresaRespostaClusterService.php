<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Agrupa respostas de texto “sujas” (mesma empresa com grafias diferentes) só em memória.
 */
class EmpresaRespostaClusterService
{
    /** @var array<string, string> */
    private array $aliases;

    /** @var list<string> */
    private const STOPWORDS = [
        'de', 'da', 'do', 'das', 'dos', 'e', 'ou', 'ltda', 'ltd', 'me', 'sa', 's', 'a', 'o',
        'no', 'na', 'nos', 'nas', 'em', 'alimentos', 'industria', 'indústria',
    ];

    public function __construct(?array $aliases = null)
    {
        $this->aliases = $aliases ?? (array) config('insight_empresa_aliases', []);
    }

    /**
     * @param  Collection<int, object{resposta: ?string, total: int|string, fator_mais_utilizado: ?string}>  $linhas
     * @return list<array{canonical: string, total: int, variants: list<array{label: string, total: int, fator: ?string}>, fator_exibido: ?string}>
     */
    public function cluster(Collection $linhas): array
    {
        $filtradas = $linhas->filter(function ($r) {
            $t = trim((string) ($r->resposta ?? ''));

            return $t !== '' && strcasecmp($t, '(em branco)') !== 0;
        })->values();

        $ordenadas = $filtradas->sortByDesc(fn ($r) => (int) $r->total)->values();

        $clusters = [];

        foreach ($ordenadas as $row) {
            $labelOriginal = trim((string) $row->resposta);
            $total = (int) $row->total;
            $fator = $row->fator_mais_utilizado ?? null;

            $canonKey = $this->canonicalKey($labelOriginal);

            $mergedIndex = null;
            foreach ($clusters as $idx => $cluster) {
                if ($this->pertenceAoCluster($canonKey, $labelOriginal, $cluster)) {
                    $mergedIndex = $idx;
                    break;
                }
            }

            if ($mergedIndex !== null) {
                $clusters[$mergedIndex]['total'] += $total;
                $clusters[$mergedIndex]['variants'][] = [
                    'label' => $labelOriginal,
                    'total' => $total,
                    'fator' => $fator,
                ];
                $clusters[$mergedIndex]['fator_exibido'] = $this->escolherFatorDominante($clusters[$mergedIndex]);
                $clusters[$mergedIndex]['canonical'] = $this->escolherCanonical($clusters[$mergedIndex]);
            } else {
                $clusters[] = [
                    'canonical' => $this->aliasOuOriginal($labelOriginal),
                    'total' => $total,
                    'variants' => [
                        ['label' => $labelOriginal, 'total' => $total, 'fator' => $fator],
                    ],
                    'fator_exibido' => $fator,
                ];
            }
        }

        usort($clusters, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $clusters;
    }

    private function canonicalKey(string $label): string
    {
        $n = $this->normalize($label);
        if (isset($this->aliases[$n])) {
            return $this->normalize($this->aliases[$n]);
        }

        return $n;
    }

    private function aliasOuOriginal(string $label): string
    {
        $n = $this->normalize($label);
        if (isset($this->aliases[$n])) {
            return $this->aliases[$n];
        }

        return $label;
    }

    /**
     * @param  array{canonical: string, total: int, variants: list<array{label: string, total: int, fator: ?string}>, fator_exibido: ?string}  $cluster
     */
    private function pertenceAoCluster(string $canonKey, string $labelOriginal, array $cluster): bool
    {
        $nLabel = $this->normalize($labelOriginal);
        $nCanon = $this->normalize($cluster['canonical']);

        foreach ($cluster['variants'] as $v) {
            $nv = $this->normalize($v['label']);
            if ($this->paresSemelhantes($nLabel, $nv)) {
                return true;
            }
            if ($this->paresSemelhantes($canonKey, $nv)) {
                return true;
            }
        }

        return $this->paresSemelhantes($nLabel, $nCanon)
            || $this->paresSemelhantes($canonKey, $nCanon);
    }

    private function paresSemelhantes(string $a, string $b): bool
    {
        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        $la = strlen($a);
        $lb = strlen($b);
        $min = min($la, $lb);
        $max = max($la, $lb);
        $long = $la >= $lb ? $a : $b;
        $short = $la < $lb ? $a : $b;

        if ($min >= 4 && $short !== '' && str_contains($long, $short)) {
            return true;
        }

        similar_text($a, $b, $pct);
        if ($pct >= 86.0) {
            return true;
        }

        if ($max > 0) {
            $lev = levenshtein($a, $b);
            if ($lev !== -1 && ($lev / $max) <= 0.24) {
                return true;
            }
        }

        return $this->tokensCompatible($a, $b);
    }

    private function tokensCompatible(string $a, string $b): bool
    {
        $ta = $this->tokensSignificativos($a);
        $tb = $this->tokensSignificativos($b);
        if (count($ta) === 0 || count($tb) === 0) {
            return false;
        }

        $menor = count($ta) <= count($tb) ? $ta : $tb;
        $maior = count($ta) > count($tb) ? $ta : $tb;
        $setMaior = array_flip($maior);

        $hits = 0;
        foreach ($menor as $t) {
            if (isset($setMaior[$t])) {
                $hits++;
            }
        }

        $ratio = $hits / max(1, count($menor));

        return $ratio >= 0.85 && count($menor) >= 2;
    }

    /** @return list<string> */
    private function tokensSignificativos(string $normalized): array
    {
        $parts = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $out = [];
        foreach ($parts as $p) {
            if (strlen($p) < 3) {
                continue;
            }
            if (in_array($p, self::STOPWORDS, true)) {
                continue;
            }
            $out[] = $p;
        }

        return $out;
    }

    private function normalize(string $s): string
    {
        $s = trim(mb_strtolower($s, 'UTF-8'));
        if (class_exists(\Normalizer::class)) {
            $s = \Normalizer::normalize($s, \Normalizer::FORM_D);
            $s = preg_replace('/\pM/u', '', $s) ?? $s;
        }
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($trans !== false) {
            $s = $trans;
        }
        $s = mb_strtolower($s, 'UTF-8');
        $s = preg_replace('/[^a-z0-9\s]/', ' ', $s) ?? $s;
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;

        return trim($s);
    }

    /**
     * @param  array{variants: list<array{label: string, total: int, fator: ?string}>}  $cluster
     */
    private function escolherFatorDominante(array $cluster): ?string
    {
        $best = null;
        $bestTotal = -1;
        foreach ($cluster['variants'] as $v) {
            if ($v['total'] > $bestTotal) {
                $bestTotal = $v['total'];
                $best = $v['fator'];
            }
        }

        return $best;
    }

    /**
     * @param  array{canonical: string, variants: list<array{label: string, total: int, fator: ?string}>}  $cluster
     */
    private function escolherCanonical(array $cluster): string
    {
        $bestLabel = $cluster['canonical'];
        $bestTotal = -1;
        foreach ($cluster['variants'] as $v) {
            if ($v['total'] > $bestTotal) {
                $bestTotal = $v['total'];
                $bestLabel = $v['label'];
            }
        }

        return $this->aliasOuOriginal($bestLabel);
    }
}
