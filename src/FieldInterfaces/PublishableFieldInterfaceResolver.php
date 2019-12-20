<?php
namespace PoP\Content\FieldInterfaces;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractSchemaFieldInterfaceResolver;

class PublishableFieldInterfaceResolver extends AbstractSchemaFieldInterfaceResolver
{
    public const NAME = 'Publishable';
    public const POST_STATUSES = [
        \POP_POSTSTATUS_PUBLISHED,
        \POP_POSTSTATUS_PENDING,
        \POP_POSTSTATUS_DRAFT,
        \POP_POSTSTATUS_TRASH,
    ];
    public function getInterfaceName(): string
    {
        return self::NAME;
    }

    public static function getFieldNamesToImplement(): array
    {
        return [
            'published',
            'not-published',
            'status',
            'is-draft',
            'is-status',
            'date',
            'datetime',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'published' => SchemaDefinition::TYPE_BOOL,
            'not-published' => SchemaDefinition::TYPE_BOOL,
            'status' => SchemaDefinition::TYPE_ENUM,
            'is-draft' => SchemaDefinition::TYPE_BOOL,
            'is-status' => SchemaDefinition::TYPE_BOOL,
            'date' => SchemaDefinition::TYPE_DATE,
            'datetime' => SchemaDefinition::TYPE_DATE,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'post-type' => $translationAPI->__('Post type', 'content'),
            'published' => $translationAPI->__('Has the post been published?', 'content'),
            'not-published' => $translationAPI->__('Has the post not been published?', 'content'),
            'status' => $translationAPI->__('Post status', 'content'),
            'is-draft' => $translationAPI->__('Is the post in \'draft\' status?', 'content'),
            'is-status' => $translationAPI->__('Is the post in the given status?', 'content'),
            'date' => $translationAPI->__('Post published date', 'content'),
            'datetime' => $translationAPI->__('Post published date and time', 'content'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        switch ($fieldName) {
            case 'date':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'format',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                            $translationAPI->__('Date format, as defined in %s', 'content'),
                            'https://www.php.net/manual/en/function.date.php'
                        ),
                        SchemaDefinition::ARGNAME_DEFAULT_VALUE => $cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:dateFormat')),
                    ],
                ];
            case 'datetime':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'format',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                            $translationAPI->__('Date and time format, as defined in %s', 'content'),
                            'https://www.php.net/manual/en/function.date.php'
                        ),
                        SchemaDefinition::ARGNAME_DEFAULT_VALUE => sprintf(
                            $translationAPI->__('\'%s\' (for current year date) or \'%s\' (otherwise)', 'content'),
                            'j M, H:i',
                            'j M Y, H:i'
                        ),
                    ],
                ];
            case 'is-status':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'status',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_ENUM,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The status to check if the post has', 'content'),
                        SchemaDefinition::ARGNAME_ENUMVALUES => self::POST_STATUSES,
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];
        }

        return parent::getSchemaFieldArgs($typeResolver, $fieldName);
    }

    public function getSchemaFieldDeprecationDescription(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $placeholder_status = $translationAPI->__('Use \'is-status(status:%s)\' instead of \'%s\'', 'content');
        $placeholder_not = $translationAPI->__('Use \'not(fieldname:%s)\' instead of \'%s\'', 'content');
        $descriptions = [
            'is-draft' => sprintf(
                $placeholder_status,
                \POP_POSTSTATUS_DRAFT,
                $fieldName
            ),
            'published' => sprintf(
                $placeholder_status,
                \POP_POSTSTATUS_PUBLISHED,
                $fieldName
            ),
            'not-published' => sprintf(
                $placeholder_not,
                'published',
                $fieldName
            ),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDeprecationDescription($typeResolver, $fieldName, $fieldArgs);
    }

    public function addSchemaDefinitionForField(array &$schemaDefinition, TypeResolverInterface $typeResolver, string $fieldName): void
    {
        switch ($fieldName) {
            case 'status':
                $schemaDefinition[SchemaDefinition::ARGNAME_ENUMVALUES] = self::POST_STATUSES;
                break;
        }
    }
}
