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

namespace App\Controller\ValidationObject;

use App\Validation\Validateable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Katarzyna Krasińska <katheroine@gmail.com>
 * @copyright Copyright (c) Katarzyna Krasińska
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/katheroine/rest-api-demo-symfony
 */
class Limitation
{
    use Validateable;

    #[Assert\PositiveOrZero()]
    #[Assert\LessThan(100)]
    private mixed $limit = null;

    #[Assert\PositiveOrZero()]
    private mixed $offset = null;

    /**
     * @param mixed $limit
     * @param mixed $offset
     *
     * @return void
     */
    public function __construct(mixed $limit, mixed $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }
}
