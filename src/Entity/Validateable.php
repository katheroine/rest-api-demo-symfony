<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Validation;

trait Validateable
{
    public function validate(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        $violations = $validator->validate($this);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
