<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class StaticInfoTablesGlobalConfigurationSchema extends GlobalConfigurationSchema
{
    /**
     * @var string
     */
    public const KEY_ENABLED_VALUE_MAPS = 'enabledValueMaps';

    /**
     * @var string
     */
    public const DEFAULT_ENABLED_VALUE_MAPS = 'countryIso2ToShortName';

    public function __construct()
    {
        parent::__construct();
        $this->getRenderingDefinition()->setLabel('Static Info Tables');

        $enabledValueMapsScript = new StringSchema(static::DEFAULT_ENABLED_VALUE_MAPS);
        $this->addProperty(static::KEY_ENABLED_VALUE_MAPS, $enabledValueMapsScript);
    }
}
