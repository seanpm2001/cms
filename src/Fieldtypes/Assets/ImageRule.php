<?php

namespace Statamic\Fieldtypes\Assets;

use Illuminate\Contracts\Validation\Rule;
use Statamic\Facades\Asset;
use Statamic\Statamic;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageRule implements Rule
{
    protected $parameters;

    public function __construct($parameters = null)
    {
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
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];

        return collect($value)->every(function ($id) use ($extensions) {
            [$extension, $guessed] = $this->getExtensions($id);

            if (! $extension) {
                return false;
            }

            return in_array($extension, $extensions)
                && in_array($guessed, $extensions);
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
        return __((Statamic::isCpRoute() ? 'statamic::' : '').'validation.image');
    }
}
