<?php

namespace Drupal\Tests\migrate_spreadsheet\Unit;

use Drupal\migrate_spreadsheet\SpreadsheetIterator;
use Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface;
use Drupal\Tests\UnitTestCase;
use PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Tests the spreadsheet iterator.
 *
 * @coversDefaultClass \Drupal\migrate_spreadsheet\SpreadsheetIterator
 */
class SpreadsheetIteratorTest extends UnitTestCase  {

  /**
   * A worksheet.
   *
   * @var \PhpOffice\PhpSpreadsheet\Worksheet
   */
  protected $worksheet;

  /**
   * The spreadsheet iterator.
   *
   * @var \Drupal\migrate_spreadsheet\SpreadsheetIteratorInterface
   */
  protected $iterator;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->iterator = (new SpreadsheetIterator())
      ->setWorksheet($this->getWorksheet())
      ->setHeaderRow(1)
      ->setColumns(['a', 'c', 'd']);
  }

  /**
   * Tests iterator rows and columns count.
   *
   * @covers ::getRowsCount
   * @covers ::getColumnsCount
   */
  public function testRowsAndColumnsCount() {
    $this->assertEquals(5, $this->iterator->getRowsCount());
    $this->assertEquals(4, $this->iterator->getColumnsCount());
  }

  /**
   * Tests headers.
   *
   * @covers ::getHeaders
   */
  public function testGetHeaders() {
    $cols = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3];
    $this->assertSame($cols, $this->iterator->getHeaders());
  }

  /**
   * Test iteration.
   *
   * @covers ::current
   */
  public function testIteration() {
    $this->iterator->setRowIndexColumn('row');

    $this->assertTrue($this->iterator->valid());
    $this->assertSame([0], $this->iterator->key());
    $this->assertSame(['row' => 0, 'a' => 'a0', 'c' => 'c0', 'd' => 'd0'], $this->iterator->current());

    // Move the cursor.
    $this->iterator->next();
    $this->assertTrue($this->iterator->valid());
    $this->assertSame([1], $this->iterator->key());
    $this->assertSame(['row' => 1, 'a' => 'a1', 'c' => 'c1', 'd' => 'd1'], $this->iterator->current());

    // Move the cursor.
    $this->iterator->next();
    $this->assertTrue($this->iterator->valid());
    $this->assertSame([2], $this->iterator->key());
    $this->assertSame(['row' => 2, 'a' => 'a2', 'c' => 'c2', 'd' => 'd2'], $this->iterator->current());

    // Move the cursor. Should run out of set.
    $this->iterator->next();
    $this->assertFalse($this->iterator->valid());

    // Rewind.
    $this->iterator->rewind();
    $this->assertTrue($this->iterator->valid());
    $this->assertSame([0], $this->iterator->key());
    $this->assertSame(['row' => 0, 'a' => 'a0', 'c' => 'c0', 'd' => 'd0'], $this->iterator->current());

    // Try to return all columns.
    $this->iterator->setColumns([]);
    $this->assertTrue($this->iterator->valid());
    $this->assertSame([0], $this->iterator->key());
    $this->assertSame(['row' => 0, 'a' => 'a0', 'b' => 'b0', 'c' => 'c0', 'd' => 'd0'], $this->iterator->current());

    // Use different primary keys.
    $this->iterator
      ->setColumns(['a', 'd'])
      ->setKeys(['b', 'c']);
    $this->assertTrue($this->iterator->valid());
    $this->assertSame(['b0', 'c0'], $this->iterator->key());
    $this->assertSame(['a' => 'a0', 'b' => 'b0', 'c' => 'c0', 'd' => 'd0'], $this->iterator->current());
  }

  /**
   * Populates a testing worksheet.
   *
   * @return \PhpOffice\PhpSpreadsheet\Worksheet
   */
  protected function getWorksheet() {
    if (!isset($this->worksheet)) {
      $this->worksheet = new Worksheet();
      $this->worksheet
        // An empty row.
        ->setCellValue('A1', '')
        ->setCellValue('B1', '')
        ->setCellValue('C1', '')
        ->setCellValue('D1', '')
        // The header row.
        ->setCellValue('A2', 'a')
        ->setCellValue('B2', 'b')
        ->setCellValue('C2', 'c')
        ->setCellValue('D2', 'd')
        // Data row with index 0.
        ->setCellValue('A3', 'a0')
        ->setCellValue('B3', 'b0')
        ->setCellValue('C3', 'c0')
        ->setCellValue('D3', 'd0')
        // Data row with index 1.
        ->setCellValue('A4', 'a1')
        ->setCellValue('B4', 'b1')
        ->setCellValue('C4', 'c1')
        ->setCellValue('D4', 'd1')
        // Data row with index 2.
        ->setCellValue('A5', 'a2')
        ->setCellValue('B5', 'b2')
        ->setCellValue('C5', 'c2')
        ->setCellValue('D5', 'd2')
      ;
    }
    return $this->worksheet;
  }

}
