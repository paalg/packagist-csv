<?php

    namespace Paalg\Csv;

    use Exception;
    use PHPUnit\Framework\TestCase;

    class CsvTest extends TestCase {

        /**
         * @var Csv
         */
        private $data;

        public function testCsv() {
            $csv = $this->getDefaultCsv();

            // Test that headers are parsed and return correctly
            $expectedHeaders = ['Year', 'Make', 'Model', 'Description', 'Price'];
            $this->assertEquals($expectedHeaders, $csv->getHeaders());

            // Iterate all lines in CSV file and assert that all rows contains the header keys
            foreach ($csv as $i => $line) {
                $keys = array_keys($line);
                $this->assertEquals($expectedHeaders, $keys);
            }
        }

        public function testCsvWithoutHeaders() {
            $csv = new Csv($this->getFullPath('example2.csv'), false);
            $this->assertNull($csv->getHeaders());
        }

        public function testCsvGetRow() {
            $csv = $this->getDefaultCsv();
            $this->assertEquals(6, $csv->count());
        }

        public function testGetRow() {
            $csv = $this->getDefaultCsv();
            $count = $csv->count();
            $this->assertEquals(6, $count, 'There should be six rows in the dataset');

            // iterate through each row
            for ($i = 1; $i<=$count; $i++) {
                $this->assertIsArray($csv->getRow($i), 'Getting row number #'.$i);
            }
        }

        public function testGetRowException1() {
            $csv = $this->getDefaultCsv();
            $this->expectException(Exception::class);
            $csv->getRow(0); // Row should be 1..n
        }

        public function testGetRowException2() {
            $csv = $this->getDefaultCsv();
            $this->expectException(Exception::class);
            $csv->getRow(7); // max row in this dataset is 6
        }

        public function testNonExistingFile() {
            $this->expectException(Exception::class);
            new Csv('/tmp/akdshfkasdhfaishdf.csv');
        }

        public function testFileIsDirectory() {
            $this->expectException(Exception::class);
            $file = $this->getFullPath('../data');
            new Csv($file);
        }

        public function testGetMaxColumns() {
            // Test that number of columsn is 5 in default dataset
            $csv = $this->getDefaultCsv();
            $this->assertEquals(5, $csv->getMaxColumns());

            // This example dataset contains 7 fields in last row
            $file = $this->getFullPath('example3.csv');
            $csv = new Csv($file);
            $this->assertEquals(7, $csv->getMaxColumns());
        }



        /**
         * Get the full path of given csv file
         * @param string $filename
         * @return string
         */
        private function getFullPath($filename) {
            $path = realpath(__DIR__.'/../../../data');
            return $path.DIRECTORY_SEPARATOR.$filename;
        }


        /**
         * @return Csv
         */
        private function getDefaultCsv() {
            if (is_null($this->data)) {
                $this->data = new Csv($this->getFullPath('example.csv'), true, ',', "\n");
            }
            return $this->data;
        }

    }
