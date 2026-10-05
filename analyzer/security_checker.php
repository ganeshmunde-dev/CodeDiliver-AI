<?php
function securityCheck($directory)
{
    $warnings = [];
    if (!is_dir($directory)) {
        return $warnings;
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
                        if (preg_match('/\$(?:_GET|_POST)\[[^\]]+\]\s*;/i', $content)) {
                            $warnings[] = "Unsanitized superglobal detected in: " . $file->getFilename();
                        }
                        if (stripos($content, 'md5(') !== false) {
                            $warnings[] = "Weak MD5 hashing found in: " . $file->getFilename();
                        }
                    }
                }
            }
        }
    } catch (Throwable $e) {
        // Fallback gracefully
    }

    return $warnings;
}
?>
