<?php declare(strict_types=1);

namespace App\Resources\Application\User\Property;

final class EmailAddress
{
    private $emailAddress;

    private function __construct(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public static function fromString(string $emailAddress): self
    {
        return new self($emailAddress);
    }

    public function toString(): string
    {
        return $this->emailAddress;
    }

    public function equals(self $emailAddress): bool
    {
        return ($emailAddress->toString() === $this->toString());
    }
}