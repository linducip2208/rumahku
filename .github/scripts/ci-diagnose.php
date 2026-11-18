<?php

/**
 * CI diagnosis helper: baca semua /tmp/junit-*.xml, laporkan tiap file
 * test yang gagal sebagai commit status (terbaca via API tanpa auth).
 *
 * Env: GITHUB_REPOSITORY, GITHUB_SHA, GITHUB_RUN_ID (bawaan Actions).
 * Butuh token dengan scope `statuses: write` (via `gh`).
 */

$repo = getenv('GITHUB_REPOSITORY') ?: '';
$sha = getenv('GITHUB_SHA') ?: '';
$runId = getenv('GITHUB_RUN_ID') ?: '';
[$owner, $name] = array_pad(explode('/', $repo, 2), 2, '');
$runUrl = $runId !== '' && $owner !== '' && $name !== ''
    ? "https://github.com/{$owner}/{$name}/actions/runs/{$runId}"
    : '';

if ($owner === '' || $sha === '') {
    fwrite(STDERR, "GITHUB_REPOSITORY/GITHUB_SHA kosong, lewati.\n");
    exit(0);
}

$files = isset($argv[1]) && $argv[1] !== '' ? [$argv[1]] : (glob('/tmp/junit-*.xml') ?: []);
if ($files === []) {
    fwrite(STDERR, "Tidak ada file junit, lewati.\n");
    exit(0);
}

/** @var array<string, string> $failures class-short-name => first message */
$failures = [];
foreach ($files as $file) {
    $xml = @simplexml_load_file($file);
    if ($xml === false) {
        continue;
    }
    foreach ($xml->xpath('//testcase[failure or error]') as $case) {
        $class = (string) ($case['classname'] ?? '');
        $short = preg_replace('/^Tests\\\\Feature\\\\/', '', $class) ?: $class;
        if (isset($failures[$short])) {
            continue;
        }
        $node = isset($case->failure) ? $case->failure : $case->error;
        $msg = trim((string) ($node['message'] ?? ''));
        if ($msg === '') {
            $msg = trim(preg_replace('/\s+/', ' ', strip_tags($node->asXML() ?: '')));
        }
        $failures[$short] = mb_substr($msg === '' ? 'gagal tanpa pesan' : $msg, 0, 120);
        if (count($failures) >= 25) {
            break 2;
        }
    }
}

if ($failures === []) {
    echo "Tidak ada kegagalan di junit.\n";
    exit(0);
}

$ok = 0;
foreach ($failures as $short => $msg) {
    $cmd = [
        'gh', 'api', "repos/{$owner}/{$name}/statuses/{$sha}",
        '-f', "state=failure",
        '-f', "context=ci-diag/{$short}",
        '-f', "description={$msg}",
    ];
    if ($runUrl !== '') {
        array_push($cmd, '-f', "target_url={$runUrl}");
    }
    $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $proc = proc_open($cmd, $descriptors, $pipes);
    $out = $proc ? stream_get_contents($pipes[1]) : '';
    $err = $proc ? stream_get_contents($pipes[2]) : '';
    if ($proc) {
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($proc);
    } else {
        $code = 1;
    }
    if ($code === 0) {
        $ok++;
        echo "OK ci-diag/{$short}\n";
    } else {
        $why = trim($err !== '' ? $err : $out);
        echo "GAGAL ci-diag/{$short} :: {$msg}".($why !== '' ? " // {$why}" : '')."\n";
    }
}

echo "Terkirim {$ok}/".count($failures)."\n";
exit(0);
