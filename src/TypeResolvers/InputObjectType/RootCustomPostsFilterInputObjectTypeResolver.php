<?php

declare(strict_types=1);

namespace PoPSchema\CustomPosts\TypeResolvers\InputObjectType;

class RootCustomPostsFilterInputObjectTypeResolver extends AbstractCustomPostsFilterInputObjectTypeResolver
{
    public function getTypeName(): string
    {
        return 'RootCustomPostsFilterInput';
    }

    public function getTypeDescription(): ?string
    {
        return $this->getTranslationAPI()->__('Input to filter custom posts', 'customposts');
    }

    protected function addCustomPostInputFields(): bool
    {
        return true;
    }
}