<?php declare(strict_types=1);

namespace App\Packages\Common\Application\HandlerResponse;

use App\Packages\Common\Application\Validation\Messages\MessageBag;

interface Success extends Response
{
    public function getWarnings(): MessageBag;
}