# Csv library
This library is for parsing an iterating through lines in a CSV file.

## Example
```php
// Load standard CSV file with headers and "," as field separator
$csv = new Csv('import.csv');
$data = $csv->getData();
print_r($data);
print_r($csv->getHeaders());
```
The output of above code can be like this:
```text
Array
(
    [0] => Array
        (
            [Year] => 1997
            [Make] => Ford
            [Model] => E350
            [Description] => ac, abs, moon
            [Price] => 3000.00
        )

    [1] => Array
        (
            [Year] => 1999
            [Make] => Chevy
            [Model] => Venture "Extended Edition"
            [Description] => 
            [Price] => 4900.00
        )
)
Array
(
    [0] => Year
    [1] => Make
    [2] => Model
    [3] => Description
    [4] => Price
)
```
If you need to iterate the CSV file, you can do this:
```php
$csv = new Csv('data.csv');
foreach ($csv as $row) {    
    print_r($row);
    foreach ($row as $fieldName => $value) {
        // do things
    }
}
```
One iteration of above script will output something like this:
```
Array
(
    [Year] => 1999
    [Make] => Chevy
    [Model] => Venture "Extended Edition"
    [Description] =>
    [Price] => 4900.00
)
```