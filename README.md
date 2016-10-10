# Migrate Spreadsheet

## Overview

The module provides a migrate source plugin for importing data from spreadsheet files. This source plugin uses the [PhpOffice/PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) library to read from the spreadsheet files.

[The supported source files](https://github.com/PHPOffice/PhpSpreadsheet#file-formats-supported) includes .ods, .xls, .xlsx, .csv.

## Usage

In your migration file:

```yaml
id: ...
source:
  plugin: spreadsheet
  # The source file. The path can be either relative to Drupal root but it can
  # be a also an absolute reference such as a stream wrapper.
  file: ../resources/source_file.xlsx
  # The worksheet to be read.
  worksheet: 'Personnel list'
  # The first row from where the table starts. Points to the row that contains
  # the table header. It's a "zero based" value. If the table row is the first
  # row, this should be 0. The value of 3 means that the table header is on the
  # fourth row.
  header_row: 3
  # Columns to be returned, basically a list of table header cell values.
  columns:
    - ID
    - 'First name'
    - 'Sure name'
    - Gender
  # If this setting is specified, the source will return also a column
  # containing the 'zero based' row index under this name. For this example,
  # 'Row index' can be used later in `keys:` list to make this column a primary
  # key column.
  row_index_column: 'Row index'
  # This points to the column or columns that provides the primary key. If is
  # missed, the current row position will be returned as primaru key.  
  keys:
    - ID
destination:
  ...
```

## Author

Claudiu Cristea ([claudiu.cristea](https://www.drupal.org/u/claudiu.cristea))
