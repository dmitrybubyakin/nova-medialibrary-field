<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\Nova;

use Laravel\Nova\Fields\Field;

class ContainerField extends Field
{
    public function __construct(string $name, array $fields)
    {
        parent::__construct($name);
        $this->withMeta(['fields' => $fields]);
    }
}
