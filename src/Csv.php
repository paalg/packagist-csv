<?php

    namespace Paalg\Csv;

    use Exception;
    use \Iterator;

/**
 * This class parses a CSV file and lets the programmer iterate the rows with for example a foraech loop.
 * @author Pål Gjerde Gammelsæter
 */
class Csv implements Iterator {

    /**
     * The iterator value for $this->data used for implementing Iterator.
     * @var int
     */
    protected $iterator;

    /**
     * If first line in csv file is headers
     * @var boolean
     */
    protected $hasHeaders;

    /**
     * An array of the field names. This is an array of integers if no
     * headers are specified.
     * @var array
     */
    protected $headers;

    /**
     * The separator value for the fields in a row.
     * @var string
     */
    protected $separator;

    /**
     * The charactor delimiting each row.
     * @var string
     */
    protected $rowdelimiter;

    /**
     * The raw data in the file.
     * @var string
     */
    protected $rawdata;

    /**
     * The processed data used for iterating.
     * @var array
     */
    protected $data;

    /**
     * @param string $file         A path to a CSV file to open
     * @param bool   $hasHeaders   Does the CSV file contain headers? This is hard to autodetect. Defaults to true.
     * @param string $separator    Separator for fields, defaults to ","
     * @param string $rowdelimiter What separates each line, defaults to \n
     * @throws Exception If file does not exist or is not a file
     */
    public function __construct($file, $hasHeaders = true, $separator = ',', $rowdelimiter = "\n") {
        if (!file_exists($file)) {
            throw new Exception('The file does not exist: '.$file);
        }
        else if (!is_file($file)) {
            throw new Exception('The given path is not a file: '.$file);
        }
        else {
            $this->rawdata = file_get_contents($file);
            $this->hasHeaders = $hasHeaders;
            $this->separator = $separator;
            $this->rowdelimiter = $rowdelimiter;
            $this->parse();
            $this->iterator = 0;
        }
    }

    /**
     * Get headers, null of no headers
     * @return array|null
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Parse the csv file and set the content to $this->data and $this->headers
     */
    private function parse() {
        $data = explode($this->rowdelimiter, $this->rawdata);   // Get all data split into rows
        $output = array(); // collect lines with columns here
        $n = 0;
        $numHeaders = 0;

        // Setting headers
        if ($this->hasHeaders) {
            $this->headers = str_getcsv($data[0], $this->separator);
            $numHeaders = count($this->headers);
            unset($data[0]);
        }
        else {
            $this->headers = null;
        }

        // Iterate data
        foreach ($data as $i => $row) {
            $row = str_getcsv($row, $this->separator, '"', "\\");

            foreach ($row as $j => $field) {
                // set the key for this field based on id or header
                if ($this->hasHeaders) {
                    $key = array_key_exists($j, $this->headers) ? $this->headers[$j] : $j;
                }
                else { $key = $j; }

                $output[$n][$key] = $field;
            }

            // If this row does not contain all fields, then add remaining fields with empty values
            if ($j<($numHeaders-1)) {
                for ($k=$j+1; $k<$numHeaders; $k++) {
                    $output[$n][$this->headers[$k]] = '';
                }
            }

            $n++;
        }

        $this->data = $output;
    }

    /**
     * Get the data as array. Each element is an associative array where keys are the field names.
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Get a row of data as array. Keys are field names (according to headers), or 0..n if there are
     * not headers.
     * @param int $n The row number, 1...n
     * @return array
     * @throws Exception If invalid row number is given, meaning <1 or >max
     */
    public function getRow($n) {
        if ($n<1 || $n>$this->count()) {
            throw new Exception('Invalid row number: '.$n);
        }
        else {
            return $this->data[$n-1];
        }
    }

    /**
     * Get the maximum fields that occurrs in the dataset.
     * This number could be higher or lower than the number of headers.
     * @return int
     */
    public function getMaxColumns() {
        $max = 0;
        foreach ($this->data as $row) {
            $count = count($row);
            if ($count > $max) { $max = $count; }
        }
        return $max;
    }

    /**
     * Get the number of data rows (not including headers)
     * @return int
     */
    public function count() {
        return count($this->data);
    }

    // --------------------------------------
    // Methods implemented for the interface:
    // --------------------------------------

    /** @return int */
    public function key() {
        return $this->iterator;
    }

    /** @return array */
    public function current() {
        return $this->data[$this->iterator];
    }

    /**
     * Increase the iterator by one
     */
    public function next() {
        $this->iterator++;
    }

    /**
     * Reset the iterator
     */
    public function rewind() {
        $this->iterator = 0;
    }

    /**
     * Check that the iterator value is valid
     * @return boolean
     */
    public function valid() {
        return ($this->iterator >= 0 && $this->iterator < count($this->data));
    }
}
