<?php

namespace App\Support;

class LocalidadeCoordenadas
{
    /** @var array<string, array{0: float, 1: float}> */
    private const CIDADES = [
        'bage' => [-31.3312, -54.1068],
        'dom pedrito' => [-30.9828, -54.6731],
        'pelotas' => [-31.7719, -52.3423],
        'osorio' => [-29.8882, -50.2697],
        'sede' => [-31.2800, -54.0900],
    ];

    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function ponto(string $cidade, string $bairro, string $sal = ''): ?array
    {
        $cidadeNorm = EmpresaNomeNormalizer::normalize($cidade);
        $base = self::CIDADES[$cidadeNorm] ?? null;
        if ($base === null) {
            return null;
        }

        $bairroNorm = EmpresaNomeNormalizer::normalize($bairro);
        $conhecidos = self::bairrosConhecidos();
        $chave = $cidadeNorm.'|'.$bairroNorm;

        if (isset($conhecidos[$chave])) {
            $lat = $conhecidos[$chave][0];
            $lng = $conhecidos[$chave][1];
        } elseif ($bairroNorm === '') {
            $lat = $base[0];
            $lng = $base[1];
        } else {
            $deslocamento = self::deslocamento($cidadeNorm, $bairroNorm, 0.018);
            $lat = $base[0] + $deslocamento[0];
            $lng = $base[1] + $deslocamento[1];
        }

        if ($sal !== '') {
            $jitter = self::deslocamento($sal, $chave, 0.004);
            $lat += $jitter[0];
            $lng += $jitter[1];
        }

        return [
            'lat' => round($lat, 6),
            'lng' => round($lng, 6),
        ];
    }

    /**
     * @return array<string, array{0: float, 1: float}>
     */
    private static function bairrosConhecidos(): array
    {
        $arquivo = database_path('data/distincao_demo_bage.php');
        $pontos = [];

        if (is_file($arquivo)) {
            $demo = require $arquivo;
            $cidade = EmpresaNomeNormalizer::normalize((string) ($demo['cidade'] ?? 'Bagé'));
            foreach ($demo['bairros'] ?? [] as $item) {
                $bairro = EmpresaNomeNormalizer::normalize((string) ($item['bairro'] ?? ''));
                if ($bairro === '' || ! isset($item['lat'], $item['lng'])) {
                    continue;
                }
                $pontos[$cidade.'|'.$bairro] = [(float) $item['lat'], (float) $item['lng']];
            }
        }

        return $pontos;
    }

    /**
     * @return array{0: float, 1: float}
     */
    private static function deslocamento(string $a, string $b, float $amplitude): array
    {
        $hash = crc32($a.'|'.$b);
        $angulo = ($hash % 360) * M_PI / 180;
        $raio = ((($hash >> 8) % 100) / 100) * $amplitude;

        return [$raio * sin($angulo), $raio * cos($angulo)];
    }
}
