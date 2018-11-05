<?php declare(strict_types=1);

namespace App\Resources\Application\User\Property;

final class Username
{
    private $username;

    private function __construct(string $username)
    {
        $this->username = $username;
    }

    public static function fromString(string $username): self
    {
        return new self($username);
    }

    public function toString(): string
    {
        return $this->username;
    }

    public function equals(self $username): bool
    {
        return ($username->toString() === $this->toString());
    }
}