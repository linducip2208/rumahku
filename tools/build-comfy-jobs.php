<?php

// Build tools/comfy-jobs.json: 1 luxury cover prompt per property (50 total).
// Run: php tools/build-comfy-jobs.php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=rumah123;charset=utf8mb4', 'root', '');
$rows = $pdo->query('SELECT id, type, city, price, title FROM properties ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

$lux = fn (int $price): string => $price >= 2000000000 ? 'palatial luxury mansion' : 'elegant modern luxury residence';

$tpl = [
    'rumah' => fn (array $r, string $l): string => "photorealistic architectural photography of a {$l} in {$r['city']}, Indonesia, contemporary tropical design, swimming pool, manicured garden, warm evening lights, ultra detailed, premium real estate",
    'villa' => fn (array $r, string $l): string => "photorealistic architectural photography of a {$l} villa in {$r['city']}, Indonesia, Balinese tropical style, private infinity pool, frangipani trees, natural stone, sunset glow, premium real estate",
    'apartemen' => fn (array $r, string $l): string => "photorealistic architectural photography of a {$l} apartment tower in {$r['city']}, Indonesia, modern high-rise with sky pool and podium garden, blue hour sky, premium real estate",
    'tanah' => fn (array $r, string $l): string => "photorealistic aerial photography of a premium vacant land plot in {$r['city']}, Indonesia, flat terrain with road access and green surroundings, golden hour, investment real estate",
    'ruko' => fn (array $r, string $l): string => "photorealistic architectural photography of a modern luxury commercial shophouse row in {$r['city']}, Indonesia, clean facades, glass storefront, morning light, premium commercial real estate",
];

$neg = 'cartoon, illustration, drawing, sketch, watermark, text, logo, distorted architecture, blurry, low quality, people close-up';

$jobs = [];
foreach ($rows as $r) {
    $fn = $tpl[$r['type']] ?? $tpl['rumah'];
    $jobs[] = [
        'property_id' => (int) $r['id'],
        'file' => "local-{$r['id']}-1.jpg",
        'positive' => $fn($r, $lux((int) $r['price'])).', no people, no text, no watermark',
        'negative' => $neg,
    ];
}

@mkdir(__DIR__, 0777, true);
file_put_contents(__DIR__.'/comfy-jobs.json', json_encode($jobs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo 'JOBS '.count($jobs).PHP_EOL;
