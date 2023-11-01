<?php

declare(strict_types=1);

namespace Imi\Bean\Util;

use const T_NS_SEPARATOR;

/**
 * Parses a file for namespaces/use/class declarations.
 *
 * @source https://github.com/doctrine/annotations/blob/2.0.x/lib/Doctrine/Common/Annotations/TokenParser.php
 */
class TokenParser
{
    /**
     * The token list.
     *
     * @phpstan-var list<mixed[]>
     */
    private array $tokens;

    /**
     * The number of tokens.
     */
    private readonly int $numTokens;

    /**
     * The current array pointer.
     */
    private int $pointer = 0;

    public function __construct(string $contents)
    {
        $this->tokens = token_get_all($contents);

        // The PHP parser sets internal compiler globals for certain things. Annoyingly, the last docblock comment it
        // saw gets stored in doc_comment. When it comes to compile the next thing to be include()d this stored
        // doc_comment becomes owned by the first thing the compiler sees in the file that it considers might have a
        // docblock. If the first thing in the file is a class without a doc block this would cause calls to
        // getDocBlock() on said class to return our long lost doc_comment. Argh.
        // To workaround, cause the parser to parse an empty docblock. Sure getDocBlock() will return this, but at least
        // it's harmless to us.
        // @phpstan-ignore-next-line
        token_get_all("<?php\n/**\n *\n */");

        $this->numTokens = \count($this->tokens);
    }

    /**
     * Gets the next non whitespace and non comment token.
     *
     * @param bool $docCommentIsComment If TRUE then a doc comment is considered a comment and skipped.
     *                                  If FALSE then only whitespace and normal comments are skipped.
     *
     * @return mixed[]|string|null the token if exists, null otherwise
     */
    public function next(bool $docCommentIsComment = true)
    {
        for ($i = $this->pointer; $i < $this->numTokens; ++$i)
        {
            ++$this->pointer;
            if (
                \T_WHITESPACE === $this->tokens[$i][0]
                || \T_COMMENT === $this->tokens[$i][0]
                || ($docCommentIsComment && \T_DOC_COMMENT === $this->tokens[$i][0])
            ) {
                continue;
            }

            return $this->tokens[$i];
        }

        return null;
    }

    /**
     * Parses a single use statement.
     *
     * @return array<string, string> a list with all found class names for a use statement
     */
    public function parseUseStatement()
    {
        $groupRoot = '';
        $class = '';
        $alias = '';
        $statements = [];
        $explicitAlias = false;
        while ($token = $this->next())
        {
            if (!$explicitAlias && \T_STRING === $token[0])
            {
                $class .= $token[1];
                $alias = $token[1];
            }
            elseif ($explicitAlias && \T_STRING === $token[0])
            {
                $alias = $token[1];
            }
            elseif (
                \PHP_VERSION_ID >= 80000
                && (\T_NAME_QUALIFIED === $token[0] || \T_NAME_FULLY_QUALIFIED === $token[0])
            ) {
                $class .= $token[1];

                $classSplit = explode('\\', (string) $token[1]);
                $alias = $classSplit[\count($classSplit) - 1];
            }
            elseif (\T_NS_SEPARATOR === $token[0])
            {
                $class .= '\\';
                $alias = '';
            }
            elseif (\T_AS === $token[0])
            {
                $explicitAlias = true;
                $alias = '';
            }
            elseif (',' === $token)
            {
                $statements[strtolower((string) $alias)] = $groupRoot . $class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            }
            elseif (';' === $token)
            {
                $statements[strtolower((string) $alias)] = $groupRoot . $class;
                break;
            }
            elseif ('{' === $token)
            {
                $groupRoot = $class;
                $class = '';
            }
            elseif ('}' === $token)
            {
                continue;
            }
            else
            {
                break;
            }
        }

        return $statements;
    }

    /**
     * Gets all use statements.
     *
     * @param string $namespaceName the namespace name of the reflected class
     *
     * @return array<string, string> a list with all found use statements
     */
    public function parseUseStatements(string $namespaceName)
    {
        $statements = [];
        while ($token = $this->next())
        {
            if (\T_USE === $token[0])
            {
                $statements = array_merge($statements, $this->parseUseStatement());
                continue;
            }

            if (\T_NAMESPACE !== $token[0] || $this->parseNamespace() !== $namespaceName)
            {
                continue;
            }

            // Get fresh array for new namespace. This is to prevent the parser to collect the use statements
            // for a previous namespace with the same name. This is the case if a namespace is defined twice
            // or if a namespace with the same name is commented out.
            $statements = [];
        }

        return $statements;
    }

    /**
     * Gets the namespace.
     *
     * @return string the found namespace
     */
    public function parseNamespace()
    {
        $name = '';
        while (
            ($token = $this->next()) && (\T_STRING === $token[0] || \T_NS_SEPARATOR === $token[0] || (
                \PHP_VERSION_ID >= 80000
                && (\T_NAME_QUALIFIED === $token[0] || \T_NAME_FULLY_QUALIFIED === $token[0])
            ))
        ) {
            $name .= $token[1];
        }

        return $name;
    }

    /**
     * Gets the class name.
     *
     * @return string the found class name
     */
    public function parseClass()
    {
        // Namespaces and class names are tokenized the same: T_STRINGs
        // separated by T_NS_SEPARATOR so we can use one function to provide
        // both.
        return $this->parseNamespace();
    }
}
