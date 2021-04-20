<?php

declare(strict_types=1);

namespace Imi\Log\Formatter;

use Imi\Log\LogLevel;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;

class ConsoleLineFormatter extends LineFormatter
{
    /**
     * 错误等级的显示样式.
     */
    protected array $levelStyles = [
        LogLevel::EMERGENCY => '<fg=red>',
        LogLevel::ALERT     => '<fg=red>',
        LogLevel::CRITICAL  => '<fg=red>',
        LogLevel::ERROR     => '<fg=red>',
        LogLevel::WARNING   => '<fg=yellow>',
        LogLevel::NOTICE    => '<fg=yellow>',
        LogLevel::INFO      => '<fg=green>',
        LogLevel::DEBUG     => '<fg=blue>',
    ];

    /**
     * 消息内容的显示样式.
     */
    protected array $messageStyles = [
        LogLevel::EMERGENCY => '<error>',
        LogLevel::ALERT     => '<error>',
        LogLevel::CRITICAL  => '<error>',
        LogLevel::ERROR     => '<error>',
        LogLevel::WARNING   => '<fg=yellow>',
        LogLevel::NOTICE    => '<fg=yellow>',
        LogLevel::DEBUG     => '<fg=blue>',
    ];

    /**
     * @param string|null $format                The format of the message
     * @param string|null $dateFormat            The format of the timestamp: one supported by DateTime::format
     * @param bool        $allowInlineLineBreaks Whether to allow inline line breaks in log entries
     */
    public function __construct(?string $format = null, ?string $dateFormat = 'Y-m-d H:i:s', bool $allowInlineLineBreaks = true, bool $ignoreEmptyContextAndExtra = true)
    {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $vars = NormalizerFormatter::format($record);

        $output = $this->format;

        foreach ($vars['extra'] as $var => $val)
        {
            if (false !== strpos($output, '%extra.' . $var . '%'))
            {
                $output = str_replace('%extra.' . $var . '%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }

        foreach ($vars['context'] as $var => $val)
        {
            if (false !== strpos($output, '%context.' . $var . '%'))
            {
                $output = str_replace('%context.' . $var . '%', $this->stringify($val), $output);
                unset($vars['context'][$var]);
            }
        }

        if ($this->ignoreEmptyContextAndExtra)
        {
            if (empty($vars['context']))
            {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (empty($vars['extra']))
            {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        foreach ($vars as $var => $val)
        {
            if (false !== strpos($output, '%' . $var . '%'))
            {
                $replace = $this->stringify($val);
                switch ($var)
                {
                    case 'level_name':
                        $style = $this->levelStyles[strtolower($vars['level_name'])] ?? null;
                        if ($style)
                        {
                            $replace = $style . $replace . '</>';
                        }
                        break;
                    case 'message':
                        $style = $this->messageStyles[strtolower($vars['level_name'])] ?? null;
                        if ($style)
                        {
                            $replace = $style . $replace . '</>';
                        }
                        break;
                }
                $output = str_replace('%' . $var . '%', $replace, $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%'))
        {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        return $output;
    }
}
