<?php
function scanProject($directory)
{
    $report = [
        'php'   => 0,
        'html'  => 0,
        'css'   => 0,
        'js'    => 0,
        'total' => 0
    ];

    if (!is_dir($directory)) {
        return $report;
    }

    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $report['total']++;
                $ext = strtolower($file->getExtension());
                if (isset($report[$ext])) {
                    $report[$ext]++;
                }
            }
        }
    } catch (Throwable $e) {
        // Fallback gracefully
    }

    return $report;
}
?>
