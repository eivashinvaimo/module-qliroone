<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Model;

use Magento\Framework\ObjectManagerInterface;
use Qliro\QliroOne\Api\Data\ContainerInterface;

/**
 * Container mapper class
 */
class ContainerMapper
{
    /**
     * @var array
     */
    private $setterTypeCache = [];

    /**
     * @var array
     */
    private $getterCache = [];

    /**
     * @var array
     */
    private $setterCache = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Recursively convert an instance of container into associative array ready for JSON payload
     *
     * @param \Qliro\QliroOne\Api\Data\ContainerInterface $container
     * @param array $mandatoryFields
     * @return array
     */
    public function toArray(ContainerInterface $container, $mandatoryFields = [])
    {
        $result = [];

        foreach ($this->getGetterKeys($container) as $key) {
            $getterName = 'get' . $key;
            $value = $container->$getterName();

            if (in_array($key, $mandatoryFields, true) || !is_null($value)) {
                if ($this->checkIfArrayWithNumericKeys($value)) {
                    $value = $this->iterateArray($value);
                } elseif ($value instanceof ContainerInterface) {
                    $value = $this->toArray($value);
                }

                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @param \Qliro\QliroOne\Api\Data\ContainerInterface|string $container
     * @return \Qliro\QliroOne\Api\Data\ContainerInterface
     */
    public function fromArray($data, $container)
    {
        if (is_string($container)) {
            $container = $this->objectManager->create($container);
        }

        $className = get_class($container);
        $keyHash = array_flip($this->getSetterKeys($container));

        foreach ($data as $key => $value) {
            $key = ucfirst($key);
            $setterName = 'set' . $key;

            if (isset($keyHash[$key])) {
                $setterType = $this->setterTypeCache[$className][$key] ?? null;

                if ($this->checkIfArrayWithNumericKeys($value)) {
                    $value = $this->iterateArray($value, $setterType);
                } elseif ($setterType) {
                    // If value is an associative array, wrap it into container
                    $subClassName = rtrim($setterType, '[]');
                    $subContainer = $this->fromArray($value, $subClassName);
                    $value = $subContainer;
                }

                $container->$setterName($value);
            }
        }

        return $container;
    }

    /**
     * Fetch a cached list of data fields in container that have getters
     *
     * @param \Qliro\QliroOne\Api\Data\ContainerInterface $container
     * @return array
     */
    private function getGetterKeys(ContainerInterface $container)
    {
        $className = get_class($container);

        if (!isset($this->getterCache[$className])) {
            $this->collectAccessors($container);
        }

        return $this->getterCache[$className];
    }

    /**
     * Fetch a cached list of data fields in container that have setters
     *
     * @param \Qliro\QliroOne\Api\Data\ContainerInterface $container
     * @return array
     */
    private function getSetterKeys(ContainerInterface $container)
    {
        $className = get_class($container);

        if (!isset($this->setterCache[$className])) {
            $this->collectAccessors($container);
        }

        return $this->setterCache[$className];
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\ContainerInterface $container
     */
    private function collectAccessors(ContainerInterface $container)
    {
        $className = get_class($container);
        $collectedGetters = [];
        $collectedSetters = [];

        foreach (get_class_methods($container) as $classMethod) {
            if (preg_match('/^get([A-Z].*)$/', $classMethod, $matches)) {
                $collectedGetters[] = $matches[1];
            } elseif (preg_match('/^set([A-Z].*)$/', $classMethod, $matches)) {
                $key = $matches[1];
                $collectedSetters[] = $key;
                $setterName = 'set' . $key;

                try {
                    $method = new \ReflectionMethod($container, $setterName);
                    $params = $method->getParameters();
                    $setterClass = $params[0]->getClass();
                    $setterType = $setterClass ? $setterClass->getName() : null;

                    if (!$setterType) {
                        $doc = $method->getDocComment();

                        if (preg_match('/@param\s+([^\s]+)\s+\$' . $params[0]->getName() . '/', $doc, $matches)) {
                            $setterType = $matches[1];

                            if (strpos($setterType, '\\') === false) {
                                $class = new \ReflectionClass($container);
                                $namespace = $class->getNamespaceName();
                                $setterType = ltrim(implode('\\', [trim($namespace, '\\'), $setterType]), '\\');

                                if (!class_exists(rtrim($setterType, '[]'))) {
                                    $setterType = null;
                                }
                            }
                        }
                    }
                } catch (\ReflectionException $e) {
                    $setterType = null;
                }

                $this->setterTypeCache[$className][$key] = $setterType;
            }
        }

        $this->getterCache[$className] = $collectedGetters;
        $this->setterCache[$className] = $collectedSetters;
    }

    /**
     * Iterate an array and convert its elements
     *
     * @param array $data
     * @param bool|string $setterType
     * @return array
     */
    private function iterateArray($data, $setterType = false)
    {
        foreach ($data as $key => $value) {
            if ($this->checkIfArrayWithNumericKeys($value)) {
                // If the value a numeric key array, iterate again each element of it
                $value = $this->iterateArray($value, $setterType);
            } elseif ($setterType === false && ($value instanceof ContainerInterface)) {
                // If value is a container, convert it to array
                $value = $this->toArray($value);
            } elseif (is_array($value)) {
                // If value is an associative array, wrap it into container
                $className = rtrim($setterType, '[]');
                $container = $this->fromArray($value, $className);
                $value = $container;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Check if the argument is an array with numeric keys starting with 0
     *
     * @param array $item
     * @return bool
     */
    private function checkIfArrayWithNumericKeys($item)
    {
        return is_array($item) && isset($item[0]);
    }
}
