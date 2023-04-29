<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Emitter;

class SseMessageEvent implements \Stringable
{
    public ?string $data = null;

    public ?string $event = null;

    public ?string $id = null;

    public ?int $retry = null;

    public ?string $comment = null;

    public function __construct(
        ?string $data = null,
        ?string $event = null,
        ?string $id = null,
        ?int $retry = null,
        ?string $comment = null
    ) {
        $this->id = $id;
        $this->event = $event;
        $this->data = $data;
        $this->retry = $retry;
        $this->comment = $comment;
    }

    public function __toString(): string
    {
        $buffer = '';
        if (null !== $this->comment)
        {
            $buffer = ": {$this->comment}\n";
        }
        if (null !== $this->event)
        {
            $buffer .= "event: {$this->event}\n";
        }
        if (null !== $this->id)
        {
            $buffer .= "id: {$this->id}\n";
        }
        if (null !== $this->retry)
        {
            $buffer .= "retry: {$this->retry}\n";
        }
        if (null !== $this->data)
        {
            $buffer .= 'data: ' . str_replace("\n", "\ndata: ", $this->data) . "\n";
        }
        if ('' === $buffer)
        {
            throw new \InvalidArgumentException('SSE message must not be empty');
        }

        return $buffer . "\n";
    }
}
