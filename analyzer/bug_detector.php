<?php
function detectBugs($directory)
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
                        if (strpos($content, 'mysql_query(') !== false) {
                            $issues[] = "Deprecated mysql_query() found in: " . $file->getFilename();
                        }
                        if (strpos($content, 'die(') !== false) {
                            $issues[] = "Uncaught die() call found in: " . $file->getFilename();
                        }
                        if (strpos($content, 'eval(') !== false) {
                            $issues[] = "Dangerous eval() function found in: " . $file->getFilename();
                        }
                    }
                }
            }
        }
    } catch (Throwable $e) {
        // Fallback gracefully without breaking analysis
    }

    return $issues;
}
?>
