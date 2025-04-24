<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class FileOrString implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param array $allowedMimes Allowed MIME types for file uploads
     * @param int $maxSize Maximum file size in kilobytes
     */
    public function __construct(
        protected array $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        protected int $maxSize = 5120 // 5MB default
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Allow null values
        if ($value === null) {
            return;
        }

        // Check if it's a string path
        if (is_string($value)) {
            // If it's a base64 image, validate it
            if (str_starts_with($value, 'data:image/')) {
                // Validate base64 image
                $this->validateBase64Image($value, $attribute, $fail);
            }
            // Otherwise, it's a regular string path, which is allowed
            return;
        }

        // Check if it's an uploaded file
        if ($value instanceof UploadedFile) {
            // Validate file type
            if (!in_array($value->getMimeType(), $this->allowedMimes)) {
                $fail("The {$attribute} must be a valid file of type: " . implode(', ', $this->allowedMimes));
            }

            // Validate file size
            if ($value->getSize() > $this->maxSize * 1024) {
                $fail("The {$attribute} may not be greater than {$this->maxSize} kilobytes.");
            }

            return;
        }

        // If we get here, the value is neither a string nor an uploaded file
        $fail("The {$attribute} must be a valid file or a string path.");
    }

    /**
     * Validate a base64 encoded image.
     *
     * @param string $value
     * @param string $attribute
     * @param Closure $fail
     */
    protected function validateBase64Image(string $value, string $attribute, Closure $fail): void
    {
        // Extract MIME type from base64 string
        $mime = null;
        if (preg_match('/^data:image\/(\w+);base64,/', $value, $matches)) {
            $mime = 'image/' . $matches[1];
        }

        // Validate MIME type
        if (!$mime || !in_array($mime, $this->allowedMimes)) {
            $fail("The {$attribute} must be a valid image of type: " . implode(', ', $this->allowedMimes));
        }

        // Validate size (approximate calculation)
        $base64Data = substr($value, strpos($value, ',') + 1);
        $decodedSize = strlen(base64_decode($base64Data));
        
        if ($decodedSize > $this->maxSize * 1024) {
            $fail("The {$attribute} may not be greater than {$this->maxSize} kilobytes.");
        }
    }
}
