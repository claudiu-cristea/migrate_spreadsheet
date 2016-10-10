<?php

namespace Drupal\migrate_spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Provides a spreadsheet iterator.
 */
class SpreadsheetIterator implements SpreadsheetIteratorInterface {

  /**
   * The worksheet object.
   *
   * @var \PhpOffice\PhpSpreadsheet\Worksheet
   */
  protected $worksheet;

  /**
   * The first row from where the table starts. It's a 'zero based' value.
   *
   * @var int
   */
  protected $headerRow = 0;

  /**
   * Columns list keyed by header cell and having column index as value.
   *
   * @var array
   */
  protected $columns = [];

  /**
   * Primary keys list keyed by header cell and having column index as value.
   *
   * @var string[]|null
   */
  protected $keys = NULL;

  /**
   * The name to be used for row index/position/delta 'zero based' value.
   *
   * @var string|null
   */
  protected $rowIndexColumn = NULL;

  /**
   * All headers keyed by cell value and having column index as value.
   *
   * @var string[]
   */
  protected $headers;

  /**
   *The total number of rows in the worksheet.
   *
   * @var integer
   */
  protected $rowsCount;

  /**
   * The total number of columns in the worksheet.
   *
   * @var integer
   */
  protected $columnsCount;

  /**
   * The relative index of the current row.
   *
   * @var int
   */
  protected $currentRow = 0;

  /**
   * {@inheritdoc}
   */
  public function key() {
    if (($keys = $this->getKeys()) === NULL) {
      // If no keys were passed, use the spreadsheet current row position.
      if (!$this->getRowIndexColumn()) {
        throw new \RuntimeException("Row index should act as key but no name has been provided. Use SpreadsheetIterator::setRowIndexColumn() to provide a name for this column.");
      }
      return [$this->currentRow];
    }

    return array_values(array_map(
      function ($column_delta) {
        return $this->getWorksheet()->getCellByColumnAndRow($column_delta, $this->getAbsoluteRowIndex(), FALSE)->getValue();
      },
      $this->getKeys()
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function valid() {
    return ($this->currentRow >= 0) && ($this->getAbsoluteRowIndex() <= $this->getRowsCount());
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->currentRow = 0;
  }

  /**
   * {@inheritdoc}
   */
  public function current() {
    if (($keys = $this->getKeys()) === NULL) {
      $row_delta_field = $this->getRowIndexColumn();
      if (!$row_delta_field) {
        throw new \RuntimeException("Row index should act as key but no name has been provided. Use SpreadsheetIterator::setRowIndexColumn() to provide a name for this column.");
      }
      $keys = [$row_delta_field => -1];
    }
    $all_columns = $keys + $this->getColumns();

    // Arrange columns in their spreadsheet native order.
    asort($all_columns);

    return array_map(
      function ($column_delta) {
        if ($column_delta === -1) {
          return $this->currentRow;
        }
        if (!$this->getWorksheet()->getCellByColumnAndRow($column_delta, $this->getAbsoluteRowIndex(), FALSE)) {
          print_r($this->key());
          print "\n";
          print_r($this->getAbsoluteRowIndex());
        }

        return $this->getWorksheet()->getCellByColumnAndRow($column_delta, $this->getAbsoluteRowIndex(), FALSE)->getValue();
      },
      $all_columns
    );
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    $this->currentRow++;
  }

  /**
   * {@inheritdoc}
   */
  public function setWorksheet(Worksheet $worksheet) {
    // Unset the computed values.
    unset($this->rowsCount, $this->columnsCount, $this->headers);
    $this->worksheet = $worksheet;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWorksheet() {
    if (!isset($this->worksheet) || !$this->worksheet instanceof Worksheet) {
      throw new \Exception('No worksheet has been set.');
    }
    return $this->worksheet;
  }

  /**
   * {@inheritdoc}
   */
  public function setColumns(array $columns) {
    $headers = $this->getHeaders();

    // If no columns were passed, all columns will be used.
    if (empty($columns)) {
      $this->columns = $headers;
    }
    else {
      $this->columns = [];
      foreach ($columns as $column) {
        if (!isset($headers[$column])) {
          throw new \RuntimeException("Column '$column' doesn't exist in the table header.");
        }
        $this->columns[$column] = $headers[$column];
      }
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumns() {
    return $this->columns;
  }

  /**
   * {@inheritdoc}
   */
  public function setKeys(array $keys) {
    if (empty($keys)) {
      $this->keys = NULL;
    }
    else {
      $headers = $this->getHeaders();
      $this->keys = [];
      foreach ($keys as $key) {
        if (!isset($headers[$key])) {
          throw new \RuntimeException("Key '$key' doesn't exist in the table header.");
        }
        $this->keys[$key] = $headers[$key];
      }
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeys() {
    return $this->keys;
  }

  /**
   * {@inheritdoc}
   */
  public function setHeaderRow($header_row) {
    $this->headerRow = $header_row;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaderRow() {
    $this->headerRow;
  }

  /**
   * {@inheritdoc}
   */
  public function setRowIndexColumn($row_index_column) {
    $this->rowIndexColumn = $row_index_column;
  }

  /**
   * {@inheritdoc}
   */
  public function getRowIndexColumn() {
    return $this->rowIndexColumn;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaders() {
    if (!isset($this->headers)) {
      for ($col = 0; $col < $this->getColumnsCount(); ++$col) {
        $value = $this->getWorksheet()->getCellByColumnAndRow($col, $this->getHeaderRow() + 2)->getValue();
        if (isset($this->headers[$value])) {
          throw new \RuntimeException("Table header '{$value}' is duplicated.");
        }
        $this->headers[$value] = $col;
      }
    }
    return $this->headers;
  }

  /**
   * {@inheritdoc}
   */
  public function getRowsCount() {
    if (!isset($this->rowsCount)) {
      $this->rowsCount = $this->getWorksheet()->getHighestDataRow();
    }
    return $this->rowsCount;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumnsCount() {
    if (!isset($this->columnsCount)) {
      $this->columnsCount = Cell::columnIndexFromString($this->getWorksheet()->getHighestDataColumn());
    }
    return $this->columnsCount;
  }

  /**
   * Gets the absolute row index.
   *
   * @return int
   */
  protected function getAbsoluteRowIndex() {
    return $this->headerRow + $this->currentRow + 2;
  }

}
