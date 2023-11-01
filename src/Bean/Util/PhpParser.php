<?php

declare(strict_types=1);

namespace Imi\Bean\Util;

/**
 * Parses a file for namespaces/use/class declarations.
 *
 * @source https://github.com/doctrine/annotations/blob/2.0.x/lib/Doctrine/Common/Annotations/PhpParser.php
 */
final class PhpParser
{
    /**
     * Parse a class or function for use statements.
     *
     * @param \ReflectionClass|\ReflectionFunction $reflection
     *
     * @psalm-return array<string, string> a list with use statements in the form (Alias => FQN).
     */
    public function parseUseStatements($reflection): array
    {
        if (method_exists($reflection, 'getUseStatements'))
        {
            return $reflection->getUseStatements();
        }

        $filename = $reflection->getFileName();

        if (false === $filename)
        {
            return [];
        }

        $content = $this->getFileContent($filename, $reflection->getStartLine());

        if (null === $content)
        {
            return [];
        }

        $namespace = preg_quote($reflection->getNamespaceName());
        $content = preg_replace('/^.*?(\bnamespace\s+' . $namespace . '\s*[;{].*)$/s', '\\1', $content);
        $tokenizer = new TokenParser('<?php ' . $content);

        return $tokenizer->parseUseStatements($reflection->getNamespaceName());
    }

    /**
     * Gets the content of the file right up to the given line number.
     *
     * @param string $filename   the name of the file to load
     * @param int    $lineNumber the number of lines to read from file
     *
     * @return string|null the content of the file or null if the file does not exist
     */
    private function getFileContent(string $filename, $lineNumber)
    {
        if (!is_file($filename))
        {
            return null;
        }

        $content = '';
        $lineCnt = 0;
        $file = new \SplFileObject($filename);
        while (!$file->eof())
        {
            if ($lineCnt++ === $lineNumber)
            {
                break;
            }

            $content .= $file->fgets();
        }

        return $content;
    }
}
