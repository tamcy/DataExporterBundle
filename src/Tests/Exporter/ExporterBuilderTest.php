<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter;


use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\ExporterFactory;
use Sparkson\DataExporterBundle\Exporter\OutputAdapter\CSVAdapter;
use Sparkson\DataExporterBundle\Exporter\Type\TypeRegistry;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\DefaultValueResolver;
use Sparkson\DataExporterBundle\Tests\Exporter\Fixtures\AddressType;
use Sparkson\DataExporterBundle\Tests\Exporter\Fixtures\ProfileType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ExporterBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $typeRegistry;

    private $valueResolver;

    private $dataSet1;

    public function setUp()
    {
        parent::setUp();

        $this->typeRegistry = new TypeRegistry();
        $this->typeRegistry->addType(new StringType());
        $this->typeRegistry->addType(new AddressType());
        $this->typeRegistry->addType(new ProfileType());
        $this->valueResolver = new DefaultValueResolver();

        $record1 = new \stdClass();
        $record1->firstName = 'Foo';
        $record1->lastName = 'Chan';
        $record1->address = new \stdClass();
        $record1->address->room = 'B';
        $record1->address->floor = 12;
        $record1->address->block = 7;

        $this->dataSet1 = [$record1];
    }

    public function testFlatStructure()
    {
        $builder = new ExporterBuilder($this->typeRegistry, $this->valueResolver);
        $builder->add('name', 'string');
        $exporter = $builder->getExporter();
        $columns = $exporter->getColumns();
        $this->assertCount(1, $columns);
        $this->assertTrue($columns->hasChild('name'));
    }

    public function testNestedStructure()
    {
        $builder = new ExporterBuilder($this->typeRegistry, $this->valueResolver);
        $builder->add('name', 'string');
        $builder->add('address', new AddressType());
        $exporter = $builder->getExporter();
        $columns = $exporter->getColumns();
        $this->assertCount(2, $columns);
        $this->assertTrue($columns->hasChild('name'));
        $addressColumn = $columns->getChild('address');
        $this->assertCount(3, $addressColumn);
        $this->assertTrue($addressColumn->hasChild('block'));
    }

    public function testRootType()
    {
        $builder = new ExporterBuilder($this->typeRegistry, $this->valueResolver, 'test_address');
        $exporter = $builder->getExporter();
        $columns = $exporter->getColumns();
        $this->assertCount(3, $columns);
        $this->assertTrue($columns->hasChild('block'));
    }

    public function testNestedRootType()
    {
        $builder = new ExporterBuilder($this->typeRegistry, $this->valueResolver, 'test_profile');
        $exporter = $builder->getExporter();
        $columns = $exporter->getColumns();
        $this->assertCount(3, $columns);
        $this->assertTrue($columns->hasChild('firstName'));
        $this->assertTrue($columns->hasChild('lastName'));
        $this->assertTrue($columns->hasChild('address'));
        $addressColumn = $columns->getChild('address');
        $this->assertCount(3, $addressColumn);
        $this->assertTrue($addressColumn->hasChild('room'));
        $this->assertTrue($addressColumn->hasChild('floor'));
        $this->assertTrue($addressColumn->hasChild('block'));

        $exporter
            ->setOutputAdapter(new CSVAdapter())
            ->setDataSet($this->dataSet1)
            ->execute();
        $result = $exporter->getResult();
        $this->assertEquals('"First Name","Last Name",Room,Floor,Block
Foo,Chan,B,12,7
', $result);
    }

    public function testRootTypeWithoutRegistryQuery()
    {
        $builder = new ExporterBuilder($this->typeRegistry, $this->valueResolver, new ProfileType());
        $exporter = $builder->getExporter();
        $columns = $exporter->getColumns();
        $this->assertCount(3, $columns);
        $this->assertTrue($columns->hasChild('firstName'));
        $this->assertTrue($columns->hasChild('lastName'));
        $this->assertTrue($columns->hasChild('address'));
        $addressColumn = $columns->getChild('address');
        $this->assertCount(3, $addressColumn);
        $this->assertTrue($addressColumn->hasChild('room'));
        $this->assertTrue($addressColumn->hasChild('floor'));
        $this->assertTrue($addressColumn->hasChild('block'));
    }

    public function testAccessingBuilderFromServiceContainer()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        $loader->load('services.yml');

        /** @var TypeRegistry $typeRegistry */
        $typeRegistry = $container->get('sparkson.data_exporter.type_registry');

        $taggedServices = $container->findTaggedServiceIds('sparkson.data_exporter.type');
        foreach ($taggedServices as $id => $tags) {
            $typeRegistry->addType($container->get($id));
        }

        /** @var ExporterFactory $exporterFactory */
        $exporterFactory = $container->get('sparkson.data_exporter.factory');
        $exporter = $exporterFactory->createBuilder()
            ->add('firstName', 'string')
            ->add('lastName', 'string')
            ->getExporter();

        $result = $exporter
            ->setOutputAdapter(new CSVAdapter())
            ->setDataSet($this->dataSet1)
            ->execute()
            ->getResult();

        $this->assertEquals('"First Name","Last Name"
Foo,Chan
', $result);
    }

}