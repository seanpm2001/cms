<?php

namespace Statamic\Events;

use Statamic\Contracts\Git\ProvidesCommitMessage;
use Statamic\Events\Concerns\TracksAuthenticatedUser;

class AssetFolderSaved extends Event implements ProvidesCommitMessage
{
    use TracksAuthenticatedUser;

    public $folder;

    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    public function commitMessage()
    {
        return __('Asset folder saved', [], config('statamic.git.locale'));
    }
}
