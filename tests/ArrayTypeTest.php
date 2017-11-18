<?php

namespace Tests\Spacegrass\ArrayType;

use PHPUnit\Framework\TestCase;
use Spacegrass\ArrayType\ArrayType;
use Tests\Spacegrass\ArrayType\Stubs\ExampleType;
use Tests\Spacegrass\ArrayType\Stubs\ExampleTypeTwo;
use Tests\Spacegrass\ArrayType\Stubs\SubExampleType;

/**
 * @group integrity-tests
 * @group array-type-tests
 */
class ArrayTypeTest extends TestCase
{
    /**
     * @dataProvider notAllowedArrayProvider
     *
     * @param string $type
     * @param array $array
     *
     * @test
     */
    public function it_does_not_allow(string $type, array $array)
    {
        $this->expectException(\TypeError::class);

        ArrayType::enforce($array, $type);
    }

    public function notAllowedArrayProvider()
    {
        return [
            'objects of different type' => [
                ExampleType::class,
                [new ExampleType, new ExampleTypeTwo]
            ],
            'non string in string list' => [
                ArrayType::STRING,
                ['string', 1]
            ],
            'non integer in integer list' => [
                ArrayType::INTEGER,
                [1, 'string']
            ],
            'non float in float list' => [
                ArrayType::FLOAT,
                [1.1, false]
            ],
            'non doubles in double list' => [
                ArrayType::DOUBLE,
                [1.1, '1']
            ],
            'non boolean in boolean list' => [
                ArrayType::DOUBLE,
                [true, 0]
            ],
            'non array in array list' => [
                ArrayType::ARRAY,
                [[], 0]
            ],
        ];
    }

    /**
     * @dataProvider allowedArrayProvider
     *
     * @param string $type
     * @param array $array
     *
     * @test
     */
    public function it_allows(string $type, array $array)
    {
        $validatedArray = ArrayType::enforce($array, $type);

        $this->assertEquals($array, $validatedArray);
    }

    public function allowedArrayProvider()
    {
        return [
            'values of the same type' => [
                ExampleType::class,
                [new ExampleType, new ExampleType]
            ],
            'subclassed values derived from the same type' => [
                ExampleType::class,
                [new ExampleType, new SubExampleType]
            ],
            'strings' => [
                ArrayType::STRING,
                ['string1', 'string2']
            ],
            'integers' => [
                ArrayType::INTEGER,
                [1, 2, 3]
            ],
            'floats' => [
                ArrayType::FLOAT,
                [1.1, 1.2]
            ],
            //PHP see floats and doubles as the same type
            'doubles' => [
                ArrayType::DOUBLE,
                [1.1, 1.2]
            ],
            'booleans' => [
                ArrayType::BOOLEAN,
                [true, false]
            ],
            'arrays' => [
                ArrayType::ARRAY,
                [[]]
            ]
        ];
    }

    /** @test */
    public function it_gives_class_name_in_error_message()
    {
        try{
            ArrayType::enforce([
                new ExampleType,
                new ExampleTypeTwo
            ], ExampleType::class);
        } catch(\TypeError $e) {
            $this->assertContains(ExampleTypeTwo::class, $e->getMessage());
        }
    }

    /** @test */
    public function it_gives_scalar_type_in_error_message()
    {
        try{
            ArrayType::enforce([
                1,
                new ExampleTypeTwo
            ], ArrayType::INTEGER);
        } catch(\TypeError $e) {
            $this->assertContains(ArrayType::INTEGER, $e->getMessage());
        }
    }
}
