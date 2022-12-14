<?php

namespace RouxtAccess\YellowDotNotifications\Messages;

class YellowDotMessage
{
    public const LINE_BREAK = '|';

    /**
     * The message content.
     *
     * @var string
     */
    private $content;

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     * @return void
     */
    public function __construct(string $content = '')
    {
        $this->content($content);
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return YellowDotMessage
     */
    public function content(string $content) : self
    {
        $this->content = trim(str_replace('<br>', '|', nl2br($content, false)));

        return $this;
    }

    /**
     * Get the message content.
     *
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }
}
