<?php
function calculateQualityScore($directory)
{
    $score = 100;
    if (!is_dir($directory)) {
        return 75;
    }

    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $filePath = $file->getPathname();
                if (is_readable($filePath) && filesize($filePath) < 3000000) {
                    $content = @file_get_contents($filePath);
                    if ($content !== false) {
                        if (strpos($content, 'mysql_query(') !== false) {
                            $score -= 10;
                        }
                        if (strpos($content, 'die(') !== false) {
                            $score -= 3;
                        }
                        if (strpos($content, '$_GET') !== false && strpos($content, 'cleanInput') === false) {
                            $score -= 4;
                        }
                    }
                }
            }
        }
    } catch (Throwable $e) {
        // Fallback gracefully
    }

    return max(0, min(100, $score));
}
?>
