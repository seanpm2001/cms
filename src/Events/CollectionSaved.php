<?php

namespace Statamic\Events;

use Statamic\Contracts\Git\ProvidesCommitMessage;

class CollectionSaved extends Event implements ProvidesCommitMessage
{
    public $collection;
    public $currentUser;

    public function __construct($collection, $currentUser = null)
    {
        $this->collection = $collection;
        $this->currentUser = $currentUser;
    }

    public function commitMessage()
    {
        return __('Collection saved', [], config('statamic.git.locale'));
    }
}
