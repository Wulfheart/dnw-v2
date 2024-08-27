<?php

namespace Dnw\Foundation\Event;

use Exception;
use PhpToken;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileBasedClassFinder implements ClassFinderInterface
{
    public function getClassesInPathRecursively(string $path): array
    {
        $files = $this->getAllFilesRecursivelyInDirectory($path);
        $classes = [];
        foreach ($files as $file) {
            $classes = array_merge($classes, $this->getClassesInFile($file));
        }

        return $classes;
    }

    /**
     * @return array<string>
     */
    private function getClassesInFile(string $fileName): array
    {
        $fileContent = file_get_contents($fileName);
        if ($fileContent === false) {
            throw new Exception('Could not read file ' . $fileName);
        }
        $tokens =  PhpToken::tokenize($fileContent);

        $classes = [];
        $namespace = '';

        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i]->getTokenName() === 'T_NAMESPACE') {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j]->getTokenName() === 'T_NAME_QUALIFIED') {
                        $namespace = $tokens[$j]->text;
                        break;
                    }
                }
            }

            if ($tokens[$i]->getTokenName() === 'T_CLASS') {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j]->getTokenName() === 'T_WHITESPACE') {
                        continue;
                    }

                    if ($tokens[$j]->getTokenName() === 'T_STRING') {
                        $classes[] = $namespace . '\\' . $tokens[$j]->text;
                    } else {
                        break;
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * @return array<string>
     */
    private function getAllFilesRecursivelyInDirectory(string $directory): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $files = [];
        foreach ($iterator as $item) {
            $path = $item->getPathname();
            if ($item->isFile()) {
                $files[] = $path;
            }
        }

        return $files;

    }
}
