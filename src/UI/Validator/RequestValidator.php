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
                if ('string' === $rule) {
                    $rules[] = new Assert\Type('string');
                } elseif (str_starts_with($rule, 'length_max')) {
                    $max = (int) explode(':', $rule)[1];
                    $rules[] = new Assert\Length(['max' => $max]);
                } elseif ('numeric' === $rule) {
                    $rules[] = new Assert\Type('numeric');
                } elseif ('positive_or_zero' === $rule) {
                    $rules[] = new Assert\PositiveOrZero();
                }
            }
            if (!empty($rules)) {
                $validationRules[$field] = $options['optional'] ?? false ? new Assert\Optional($rules) : $rules;
            }
        }

        $violations = $this->validator->validate($data, new Assert\Collection($validationRules));

        return iterator_to_array($violations);
    }
}
