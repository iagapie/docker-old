<?php
/**
 * Copyright © 2018 RedBooth Lab SRL. All rights reserved.
 * @author Igor Agapie <iagapie@rblab.it>
 */

namespace App\Docker\API\Normalizer;

class NetworkSettingsNormalizer extends \Docker\API\Normalizer\NetworkSettingsNormalizer
{
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (property_exists($data, 'Ports') && $data->{'Ports'} !== null) {
            $values = [];

            foreach ($data->{'Ports'} as $key => $value) {
                $values[$key] = $value ?: [];
            }

            $data->{'Ports'} = $values;
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
