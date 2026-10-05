<?php
function performanceCheck($directory)
{
    $issues = [];
    if (!is_dir($directory)) {
        return $issues;
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
                        if (stripos($content, 'SELECT *') !== false) {
                            $issues[] = "Avoid using SELECT * in: " . $file->getFilename();
                        }
                        if (substr_count($content, 'mysqli_query(') > 10) {
                            $issues[] = "High query density detected in: " . $file->getFilename();
                        }
                    }
                }
            }
        }
    } catch (Throwable $e) {
        // Fallback gracefully
    }

    return $issues;
}
?>
