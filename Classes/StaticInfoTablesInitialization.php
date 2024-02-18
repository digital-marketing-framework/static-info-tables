<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\StaticInfoService;

class StaticInfoTablesInitialization extends Initialization
{
    protected function getGlobalConfigurationSchema(): ?SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setLabel('Static Info Tables');

        $names = $this->staticInfoService->getAllMapNames();
        $enabledValueMapsSchema = new StringSchema('countryIso2ToShortName');
        $enabledValueMapsSchema->getAllowedValues()->addValue('', '-- ALL --');
        foreach ($names as $name) {
            $enabledValueMapsSchema->addValueToValueSet('staticInfoTables/enabledValueMaps', $name);
        }
        $enabledValueMapsSchema->getAllowedValues()->addValueSet('staticInfoTables/enabledValueMaps');
        // $enabledValueMapsSchema->getRenderingDefinition()->setLabel('Enabled Value Maps [' . implode(', ', $names) .']');
        $enabledValueMapsSchema->getRenderingDefinition()->setLabel('Enabled Value Maps');
        $enabledValueMapsSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $schema->addProperty('enabledValueMaps', $enabledValueMapsSchema);

        return $schema;
    }

    public function __construct(
        protected StaticInfoService $staticInfoService,
    ) {
        parent::__construct('static-info-tables', '1.0.0', 'dmf_static_info_tables');
    }
}
