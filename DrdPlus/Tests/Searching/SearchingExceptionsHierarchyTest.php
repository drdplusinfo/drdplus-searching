<?php
namespace DrdPlus\Tests\Searching;

use Granam\Tests\Exceptions\Tools\AbstractExceptionsHierarchyTest;

class SearchingExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        return str_replace('\\\Tests', '', __NAMESPACE__);
    }

}