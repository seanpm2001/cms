<?php

namespace Statamic\Fieldtypes\Assets;

use Illuminate\Contracts\Validation\Rule;
use Statamic\Facades\Asset;
use Statamic\Statamic;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MimesRule implements Rule
{
    protected $parameters;

    public function __construct($parameters)
    {
        if (in_array('jpg', $parameters) || in_array('jpeg', $parameters)) {
            $parameters = array_unique(array_merge($parameters, ['jpg', 'jpeg']));
        }

        $this->parameters = $parameters;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return collect($value)->every(function ($id) {
            [$extension, $guessed] = $this->getExtensions($id);

            if (! $extension) {
                return false;
            }

            return in_array($guessed, $this->parameters)
                && in_array($extension, $this->parameters);
        });
    }

    private function getExtensions($item)
    {
        if ($item instanceof UploadedFile) {
            return [$item->getClientOriginalExtension(), $item->guessExtension()];
        }

        if (! $asset = Asset::find($item)) {
            return [null, null];
        }

        return [$asset->extension(), $asset->guessedExtension()];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return str_replace(':values', implode(', ', $this->parameters), __((Statamic::isCpRoute() ? 'statamic::' : '').'validation.mimes'));
    }
}
