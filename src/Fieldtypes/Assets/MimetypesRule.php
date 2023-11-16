<?php

namespace Statamic\Fieldtypes\Assets;

use Illuminate\Contracts\Validation\Rule;
use Statamic\Facades\Asset;
use Statamic\Statamic;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

class MimetypesRule implements Rule
{
    protected $parameters;

    public function __construct($parameters)
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
        return collect($value)->every(function ($id) {
            [$extension, $mimeType] = $this->getExtensionAndMimeType($id);

            if (! $extension) {
                return false;
            }

            $validMime = in_array($mimeType, $this->parameters) || in_array(explode('/', $mimeType)[0].'/*', $this->parameters);

            $validExtension = in_array($extension, MimeTypes::getDefault()->getExtensions($mimeType));

            return $validMime && $validExtension;
        });
    }

    private function getExtensionAndMimeType($item)
    {

        if ($item instanceof UploadedFile) {
            return [$item->getClientOriginalExtension(), $item->getMimeType()];
        }

        if (! $asset = Asset::find($item)) {
            return [null, null];
        }

        return [$asset->extension(), $asset->mimeType()];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return str_replace(':values', implode(', ', $this->parameters), __((Statamic::isCpRoute() ? 'statamic::' : '').'validation.mimetypes'));
    }
}
