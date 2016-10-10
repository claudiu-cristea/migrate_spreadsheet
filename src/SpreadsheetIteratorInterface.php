<?php

namespace Drupal\migrate_spreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Provides an interface for spreadsheet iterators.
 */
interface SpreadsheetIteratorInterface extends \Iterator{

  /**
   * Sets the worksheet object.
   *
   * @param \PhpOffice\PhpSpreadsheet\Worksheet $worksheet
   *   The spreadsheet worksheet.
   *
   * @return $this
   */
  public function setWorksheet(Worksheet $worksheet);

  /**
   * Gets the worksheet.
   *
   * @return \PhpOffice\PhpSpreadsheet\Worksheet
   *
   * @see \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface::setWorksheet()
   */
  public function getWorksheet();

  /**
   * Sets the list of relevant columns to be returned.
   *
   * @param string[] $columns
   *   An indexed array of columns.
   *
   * @return $this
   *
   * @throws \RuntimeException
   *   If a columns does not exist in the header.
   */
  public function setColumns(array $columns);

  /**
   * Gets the list of columns.
   *
   * @return string[]
   *   The list of columns.
   *
   * @see \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface::setColumns()
   */
  public function getColumns();

  /**
   * Sets the list of columns that arge giving the primary key.
   *
   * In nothing is passed, the iterator will return the index of the current row
   * relative to the table header.
   *
   * @param string[] $keys
   *   A list of columns that are defining the primary index.
   *
   * @return $this
   *
   * @throws \RuntimeException
   *   If a key does not exist in the header.
   */
  public function setKeys(array $keys);

  /**
   * Gets the list of columns that arge giving the primary key.
   *
   * @return string[]
   *   A list of column names.
   *
   * @see \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface::setKeys()
   */
  public function getKeys();

  /**
   * Sets the index of the first row from where the table starts.
   *
   * It's a 'zero based' value that points to the row that contains
   * the table header. If the table row is the first this should be 0. A value
   * of 3 would mean that the table header is on the fourth row.
   *
   * @param int $header_row
   *   The header row index.
   *
   * @return $this
   */
  public function setHeaderRow($header_row);

  /**
   * Gets the header row index.
   *
   * @return int
   *
   * @see \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface::setHeaderRow()
   */
  public function getHeaderRow();

  /**
   * Sets the row index column name.
   *
   * The 'row index column' is a pseudo-column, that not exist on the worksheet,
   * containing the 'zero based' current index/position/delta of each row. The
   * caller can use this method to set a name for that column. If a name was set
   * that column will be also outputted along with the row, in ::current(). The
   * name can be passed also in ::setKeys() list. In that case row index will be
   * or will be part of the primary key.
   *
   * @param string $row_index_column
   *   The name to be used for the row index/position/delta columsn.
   *
   * @return $this
   *
   * @see \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface::setKeys()
   */
  public function setRowIndexColumn($row_index_column);

  /**
   * Gets the name of the row index column.
   *
   * @return string
   *
   * @see \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface::setRowIndexColumn()
   */
  public function getRowIndexColumn();

  /**
   * Retrieves a full list of headers.
   *
   * @return string[]
   *   An array having the column index as key and header name as value.
   */
  public function getHeaders();

  /**
   * Gets the total number of rows in the worksheet.
   *
   * @return int
   */
  public function getRowsCount();

  /**
   * Gets the total number of columns in the worksheet.
   *
   * @return int
   */
  public function getColumnsCount();

}
