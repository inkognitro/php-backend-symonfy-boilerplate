<?php declare(strict_types=1);

namespace App\Resources\Application\Language\Attributes;

use App\Resources\Application\Attribute;
use App\Resources\Application\AttributeTypeId;

final class LanguageId implements Attribute
{
    private $id;

    public static function getPayloadKey(): string
    {
        return 'id';
    }

    public static function getTypeId(): AttributeTypeId
    {
        return AttributeTypeId::uuid();
    }

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toString(): string
    {
        return strtolower($this->id);
    }

    public static function english(): self
    {
        return new self('en');
    }

    public static function german(): self
    {
        return new self('de');
    }

    public function equals(self $that): bool
    {
        return (strcasecmp($this->toString(), $that->toString()) === 0);
    }
}