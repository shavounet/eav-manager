<?php

namespace CleverAge\EAVManager\ImportBundle\Model;

use CleverAge\EAVManager\ImportBundle\Import\EAVDataImporter;
use Sidus\EAVModelBundle\Configuration\FamilyConfigurationHandler;
use Sidus\EAVModelBundle\Exception\MissingFamilyException;
use Sidus\EAVModelBundle\Model\FamilyInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Handle import configuration
 */
class ImportConfig
{
    /** @var string */
    protected $code;

    /** @var string */
    protected $filePath;

    /** @var FamilyInterface */
    protected $family;

    /** @var EAVDataImporter */
    protected $service;

    /** @var array */
    protected $mapping;

    /** @var array */
    protected $context;

    /** @var array */
    protected $options;

    /**
     * ImportConfig constructor.
     *
     * @param string                     $code
     * @param FamilyConfigurationHandler $familyConfigurationHandler
     * @param array                      $configuration
     * @throws MissingFamilyException
     * @throws AccessException
     * @throws InvalidArgumentException
     * @throws UnexpectedTypeException
     */
    public function __construct($code, FamilyConfigurationHandler $familyConfigurationHandler, $configuration)
    {
        $this->code = $code;

        $this->family = $familyConfigurationHandler->getFamily($configuration['family']);
        unset($configuration['family']);

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($configuration as $key => $value) {
            $accessor->setValue($this, $key, $value);
        }
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return FamilyInterface
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param FamilyInterface $family
     */
    public function setFamily(FamilyInterface $family)
    {
        $this->family = $family;
    }

    /**
     * @return EAVDataImporter
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param EAVDataImporter $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param array $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getOption($code)
    {
        if (!isset($this->options[$code])) {
            return null;
        }

        return $this->options[$code];
    }

    /**
     * @param string $code
     * @param mixed  $value
     */
    public function addOption($code, $value)
    {
        $this->options[$code] = $value;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}