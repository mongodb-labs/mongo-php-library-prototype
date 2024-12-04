# Code Generator for MongoDB PHP Library

This subproject is used to generate the code that is committed to the repository.
The `generator` directory is not included in `mongodb/mongodb` package and is not installed by Composer.

## Contributing

Updating the generated code can be done only by modifying the code generator, or its configuration.

To run the generator, you need to have PHP 8.1+ installed and Composer.

1. Move to the `generator` directory: `cd generator`
2. Install dependencies: `composer install`
3. Run the generator: `./generate`

## Configuration

The `generator/config/*.yaml` files contains the list of operators and stages that are supported by the library.

### Arguments

| Field         | Type                | Description                                                                                                                                                               |
|---------------|---------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `name`        | `string`            | The name of the argument. If it starts with `$`, the dollar is trimmed from the class property name                                                                       |
| `type`        | list of `string`    | The list of accepted types                                                                                                                                                |
| `description` | `string`            | The description of the argument from MongoDB's documentation                                                                                                              |
| `optional`    | `boolean`           | Whether the argument is optional or not                                                                                                                                   |
| `valueMin`    | `number`            | The minimum value for a numeric argument                                                                                                                                  |
| `valueMax`    | `number`            | The maximum value for a numeric argument                                                                                                                                  |
| `variadic`    | `string`            | If sent, the argument is variadic. Defines the format `array` for a list or `object` for a map                                                                            |
| `variadicMin` | `integer`           | The minimum number of arguments for a variadic parameter                                                                                                                  |
| `default`     | `scalar` or `array` | The default value for the argument                                                                                                                                        |
| `mergeObject` | `bool`              | Default `false`. If `true`, the value must be an object and the properties of the value object are merged into the parent operator. `$group` stage uses it for the fields |

### Test pipelines

Each operator can contain a `tests` section with a list if pipelines. To represent specific BSON objects, it is necessary to use Yaml tags:

| BSON Type   | Example                                                |
|-------------|--------------------------------------------------------|
| Regex       | `!bson_regex '^abc'` <br/> `!bson_regex ['^abc', 'i']` |
| Int64       | `!bson_int64 '123456789'`                              |
| Decimal128  | `!bson_decimal128 '0.9'`                               |
| UTCDateTime | `!bson_utcdatetime 0`                                  |
| ObjectId    | `!bson_ObjectId '5a9427648b0beebeb69589a1`             |
| Binary      | `!bson_binary 'IA=='`                                  |
| Binary UUID | `!bson_uuid 'fac32260-b511-4c69-8485-a2be5b7dda9e'`    |

To add new test cases to operators, you can get inspiration from the official MongoDB documentation and use the `generator/js2yaml.html` web page to manually convert a pipeline array from JS to Yaml.
