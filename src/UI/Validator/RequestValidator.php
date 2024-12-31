<?php

declare(strict_types=1);

namespace App\UI\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class RequestValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(array $data, array $constraints): array
    {
        $validationRules = [];

        foreach ($constraints as $field => $options) {
            $rules = [];

            foreach ($options['rules'] as $rule) {
                $constraint = match (true) {
                    'string' === $rule => new Assert\Type('string'),
                    str_starts_with($rule, 'length_max') => new Assert\Length(['max' => (int) explode(':', $rule)[1]]),
                    'numeric' === $rule => new Assert\Type('numeric'),
                    'positive_or_zero' === $rule => new Assert\PositiveOrZero(),
                    str_starts_with($rule, 'min:') => new Assert\GreaterThan((int) substr($rule, 4) - 1),
                    default => null,
                };

                if ($constraint) {
                    $rules[] = $constraint;
                }
            }

            if (!empty($rules)) {
                $validationRules[$field] = $options['optional'] ?? false
                    ? new Assert\Optional($rules)
                    : $rules;
            }
        }

        $violations = $this->validator->validate($data, new Assert\Collection($validationRules));

        return iterator_to_array($violations);
    }
}
