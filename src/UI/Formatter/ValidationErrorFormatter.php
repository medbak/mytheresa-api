<?php

declare(strict_types=1);

namespace App\UI\Formatter;

use Symfony\Component\Validator\ConstraintViolation;

class ValidationErrorFormatter
{
    public function formatValidationErrors(array $violations): array
    {
        return [
            'status' => 'error',
            'message' => 'Invalid input parameters',
            'errors' => array_map(
                fn (ConstraintViolation $violation) => [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ],
                $violations
            ),
        ];
    }
}
