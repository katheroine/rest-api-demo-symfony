<?php

declare(strict_types=1);

/*
 * This file is part of REST API Demo Symfony application.
 *
 * (c) Katarzyna Krasińska
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace App\Validation;

use Symfony\Component\Validator\Validation;

/**
 * @author Katarzyna Krasińska <katheroine@gmail.com>
 * @copyright Copyright (c) Katarzyna Krasińska
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/katheroine/rest-api-demo-symfony
 */
trait Validateable
{
    /**
     * @return array
     */
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
