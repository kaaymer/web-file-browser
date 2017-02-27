<?php

namespace Villermen\WebFileBrowser;

use Exception;

class Path
{
    /**
     * @param string $path
     * @param bool $resolve
     * @return string
     *
     * @throws Exception
     */
    public static function normalizeFile(string $path, bool $resolve = false): string
    {
        if ($resolve) {
            $path = realpath($path);

            if (!$path) {
                throw new Exception("Given path does not exist.");
            }
        }

        $path = str_replace("\\", "/", $path);

        // Remove optional scheme to add back later
        $scheme = "";
        $path = preg_replace_callback("/^[a-z0-9+\.\-]+:\/\//", function ($matches) use (&$scheme) {
            $scheme = $matches[0];
            return "";
        }, $path);

        $replacements = 0;
        do {
            $path = str_replace(["/./", "//"], "/", $path, $replacements);
        } while ($replacements > 0);

        return $scheme . $path;
    }

    /**
     * @param string $path
     * @param bool $resolve
     * @return string
     */
    public static function normalizeDirectory(string $path, $resolve = false)
    {
        return rtrim(self::normalizeFile($path, $resolve), "/") . "/";
    }

    /**
     * Paths in PHP on Windows apparently use Windows-1252 encoding...
     *
     * @param string $path
     * @return string
     */
    public static function fixEncoding(string $path) : string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
            return mb_convert_encoding($path, "UTF-8", "Windows-1252");
        }

        return $path;
    }

    /**
     * The inverse of the fixEncoding function.
     *
     * @param string $path
     * @return string
     */
    public static function breakEncoding(string $path) : string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
            return mb_convert_encoding($path, "Windows-1252", "UTF-8");
        }

        return $path;
    }
}