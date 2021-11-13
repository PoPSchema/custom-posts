<?php

declare(strict_types=1);

namespace PoPSchema\CustomPosts\TypeResolvers\EnumType;

use PoP\ComponentModel\TypeResolvers\EnumType\AbstractEnumTypeResolver;
use PoPSchema\CustomPosts\Enums\CustomPostContentFormatEnum;

class CustomPostContentFormatEnumTypeResolver extends AbstractEnumTypeResolver
{
    public function getTypeName(): string
    {
        return 'CustomPostContentFormatEnum';
    }
    /**
     * @return string[]
     */
    public function getEnumValues(): array
    {
        return [
            CustomPostContentFormatEnum::HTML,
            CustomPostContentFormatEnum::PLAIN_TEXT,
        ];
    }
}
