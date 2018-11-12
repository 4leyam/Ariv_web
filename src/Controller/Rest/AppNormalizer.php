<?php
/**
 * Created by PhpStorm.
 * User: ryans
 * Date: 10-11-2018
 * Time: 10:57
 */

namespace App\Controller\Rest;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AppNormalizer extends ObjectNormalizer
{

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null
                                , NameConverterInterface $nameConverter = null
                                , PropertyAccessorInterface $propertyAccessor = null
                                , PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        parent::__construct($classMetadataFactory , $nameConverter , $propertyAccessor , $propertyTypeExtractor);


        $this->setCircularReferenceLimit(0);
        $this->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
    }


}