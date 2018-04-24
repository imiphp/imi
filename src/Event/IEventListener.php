<?php
namespace Imi\Event;

interface IEventListener
{
	public function handle(EventParam $e);
}