<?php

namespace Ulff\BehatRestApiExtension\Context;

use Ulff\BehatRestApiExtension\Exception as Exception;
use Behat\Gherkin\Node\TableNode;
use Codifico\ParameterBagExtension\Context\ParameterBagDictionary;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class RestApiContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use ParameterBagDictionary;
    use KernelDictionary;

    private $headers = [];

    /**
     * @When I make request :method :uri
     */
    public function iMakeRequest($method, $uri)
    {
        $uri = $this->extractFromParameterBag($uri);
        $this->request($method, $uri);
    }

    /**
     * @When I make request :method :uri with params:
     */
    public function iMakeRequestWithParams($method, $uri, TableNode $table)
    {
        $uri = $this->extractFromParameterBag($uri);
        $params = [];
        foreach($table->getRowsHash() as $field => $value) {
            if(preg_match('/array\(.*\)/', $value)) {
                $anArray = [];
                eval("\$anArray = $value;");
                $params[$field] = $anArray;
            } else {
                $params[$field] = $this->getParameterBag()->replace($value);
            }
        }
        $this->request($method, $uri, $params);
    }

    /**
     * @Then the response should be JSON
     */
    public function theResponseShouldBeJson()
    {
        $response = $this->getClient()->getResponse()->getContent();
        if(json_decode($response) === null) {
            throw new Exception\JsonExpectedException();
        }
    }

    /**
     * @Then the response JSON should be a collection
     */
    public function theResponseJsonShouldBeACollection()
    {
        $response = $this->getResponseContentJson();
        if(!is_array($response)) {
            throw new Exception\CollectionExpectedException();
        }
        return;
    }

    /**
     * @Then the response JSON should be a single object
     */
    public function theResponseJsonShouldBeASingleObject()
    {
        $response = $this->getResponseContentJson();
        if(!is_object($response)) {
            throw new Exception\SingleObjectExpectedException();
        }
        return;
    }

    /**
     * @Then the response JSON should have :property field
     */
    public function theRepsonseJsonShouldHaveField($property)
    {
        $response = $this->getResponseContentJson();
        $this->assertDocumentHasProperty($response, $property);
        return;
    }

    /**
     * @Then the response JSON should have :property field with value :expectedValue
     */
    public function theRepsonseJsonShouldHaveFieldWithValue($property, $expectedValue)
    {
        $expectedValue = $this->extractFromParameterBag($expectedValue);
        $response = $this->getResponseContentJson();
        $this->assertDocumentHasPropertyWithValue($response, $property, $expectedValue);
        return;
    }

    /**
     * @Then the response JSON should have :property field set to :expectedValue
     */
    public function theRepsonseJsonShouldHaveFieldSetTo($property, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        $this->assertDocumentHasPropertyWithBooleanValue($response, $property, $expectedValue);
        return;
    }

    /**
     * @Then the response JSON should have :property fields with array :expectedArray as value
     */
    public function theResponseJsonShouldHaveFieldsWithArrayAsValue($property, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        $this->assertDocumentHasPropertyWithArrayAsValue($response, $property, $expectedValue);
        return;
    }

    /**
     * @Then all response collection items should have :property field
     */
    public function allResponseCollectionItemsShouldHaveField($property)
    {
        $response = $this->getResponseContentJson();
        foreach($response as $document) {
            $this->assertDocumentHasProperty($document, $property);
        }
        return;
    }

    /**
     * @Then all response collection items should have :property field with value :expectedValue
     */
    public function allResponseCollectionItemsShouldHaveFieldWithValue($property, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        foreach($response as $document) {
            $this->assertDocumentHasPropertyWithValue($document, $property, $expectedValue);
        }
        return;
    }

    /**
     * @Then all response collection items should have nested field :property with value :expectedValue
     */
    public function allResponseCollectionItemsShouldHaveNestedFieldWithValue($property, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        foreach($response as $document) {
            $this->assertDocumentHasNestedPropertyWithValue($document, $property, $expectedValue);
        }
        return;
    }

    /**
     * @Then all response collection items should have :property field set to :expectedBoolean
     */
    public function allResponseCollectionItemsShouldHaveFieldSetTo($property, $expectedBoolean)
    {
        $response = $this->getResponseContentJson();
        if(empty($response)) {
            throw new Exception\EmptyCollectionException();
        }
        foreach($response as $document) {
            $this->assertDocumentHasPropertyWithBooleanValue($document, $property, $expectedBoolean);
        }
        return;
    }

    /**
     * @Then the response JSON :fieldName field should be a collection
     */
    public function theResponseJsonFieldShouldBeACollection($fieldName)
    {
        $response = $this->getResponseContentJson();
        if(!is_array($response->$fieldName)) {
            throw new Exception\CollectionExpectedException();
        }
        return;
    }

    /**
     * @Then all nested :collectionFieldName collection items should have :nestedFieldName field
     */
    public function allNestedCollectionItemsShouldHaveField($collectionFieldName, $nestedFieldName)
    {
        $response = $this->getResponseContentJson();
        if(empty($response->$collectionFieldName)) {
            throw new Exception\EmptyCollectionException($collectionFieldName);
        }
        foreach($response->$collectionFieldName as $document) {
            $this->assertDocumentHasProperty($document, $nestedFieldName);
        }
        return;
    }

    /**
     * @Then all nested :collectionFieldName collection items should have :nestedFieldName field set to :expectedValue
     */
    public function allNestedCollectionItemsShouldHaveFieldSetTo($collectionFieldName, $nestedFieldName, $expectedValue)
    {
        $expectedBoolean = ($expectedValue == 'true' ? true : false);
        $response = $this->getResponseContentJson();
        if(empty($response->$collectionFieldName)) {
            throw new Exception\EmptyCollectionException($collectionFieldName);
        }
        foreach($response->$collectionFieldName as $document) {
            $this->assertDocumentHasPropertyWithBooleanValue($document, $nestedFieldName, $expectedBoolean);
        }
        return;
    }

    /**
     * @Then all nested :collectionFieldName collection items should have :nestedFieldName field with value :expectedValue
     */
    public function allNestedCollectionItemsShouldHaveFieldWithValue($collectionFieldName, $nestedFieldName, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        if(empty($response->$collectionFieldName)) {
            throw new Exception\EmptyCollectionException($collectionFieldName);
        }
        foreach($response->$collectionFieldName as $document) {
            $this->assertDocumentHasPropertyWithValue($document, $nestedFieldName, $expectedValue);
        }
        return;
    }

    /**
     * @Then all nested :collectionFieldName collection items should have nested :nestedFieldName field with value :expectedValue
     */
    public function allNestedCollectionItemsShouldHaveNestedFieldWithValue($collectionFieldName, $nestedFieldName, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        if(empty($response->$collectionFieldName)) {
            throw new Exception\EmptyCollectionException($collectionFieldName);
        }
        foreach($response->$collectionFieldName as $document) {
            $this->assertDocumentHasNestedPropertyWithValue($document, $nestedFieldName, $expectedValue);
        }
        return;
    }

    /**
     * @Then the response JSON should have nested :arg1 field with value :arg2
     */
    public function theResponseJsonShouldHaveNestedFieldWithValue($nestedFieldName, $expectedValue)
    {
        $response = $this->getResponseContentJson();
        $this->assertDocumentHasNestedPropertyWithValue($response, $nestedFieldName, $expectedValue);
        return;
    }

    private function request($method, $uri, array $params = array(), array $headers = array())
    {
        $headers = array_merge($headers, $this->headers);
        $server = $this->createServerArray($headers);
        $this->getClient()->request($method, $this->locatePath($uri), $params, array(), $server);
    }

    private function createServerArray(array $headers = array())
    {
        $server = array();
        $nonPrefixed = array('CONTENT_TYPE');
        foreach ($headers as $name => $value) {
            $headerName = strtoupper(str_replace('-', '_', $name));
            $headerName = in_array($headerName, $nonPrefixed) ? $headerName : 'HTTP_'.$headerName;
            $server[$headerName] = $value;
        }
        return $server;
    }

    private function getClient()
    {
        $driver = $this->getSession()->getDriver();
        return $driver->getClient();
    }

    private function extractFromParameterBag($string)
    {
        $string = $this->getParameterBag()->replace($string);
        return $string;
    }

    private function getResponseContentJson()
    {
        return json_decode($this->getClient()->getResponse()->getContent());
    }

    private function assertDocumentHasProperty($document, $property)
    {
        if(!isset($document->$property)) {
            throw new Exception\NotFoundPropertyException($property);
        }
    }

    private function assertDocumentHasPropertyWithValue($document, $property, $expectedValue)
    {
        $this->assertDocumentHasProperty($document, $property);
        if($document->$property != $expectedValue) {
            throw new Exception\IncorrectPropertyValueException($property, $expectedValue, $document->$property);
        }
    }

    private function assertDocumentHasNestedPropertyWithValue($document, $property, $expectedValue)
    {
        $nestedNode = explode('->', $property);
        $documentAsArray = (array) $document;
        foreach($nestedNode as $node) {
            if(!isset($documentAsArray[$node])) {
                throw new Exception\NotFoundPropertyException($property);
            }
            $documentAsArray = (array) $documentAsArray[$node];
        }
        $documentAsArray = reset($documentAsArray);
        $expectedValue = $this->extractFromParameterBag($expectedValue);

        if($documentAsArray !== $expectedValue) {
            throw new Exception\IncorrectPropertyValueException($property, $expectedValue, $documentAsArray);
        }
    }

    private function assertDocumentHasPropertyWithBooleanValue($document, $property, $expectedValue)
    {
        $expectedBoolean = ($expectedValue == 'true' ? true : false);
        $this->assertDocumentHasProperty($document, $property);
        if($document->$property !== $expectedBoolean) {
            throw new Exception\IncorrectPropertyValueException($property, $expectedValue, $document->$property === true ? 'true' : 'false');
        }
    }

    private function assertDocumentHasPropertyWithArrayAsValue($document, $property, $expectedValue)
    {
        $this->assertDocumentHasProperty($document, $property);
        $anArray = [];
        eval("\$anArray = $expectedValue;");
        if(!is_array($anArray)) {
            throw new Exception\ArrayExpectedException($property, $expectedValue, $document->$property);
        }
        if($anArray !== $document->$property) {
            throw new Exception\IncorrectPropertyValueException($property, var_export($anArray, true), var_export($document->$property, true));
        }
    }
}
