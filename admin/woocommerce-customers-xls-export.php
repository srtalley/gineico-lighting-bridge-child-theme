<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// include PHPSpreadsheet
require_once( HFPSP_VENDOR_PATH . 'autoload.php' );
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;

global $wpdb;

// Get Website Title
$blog_title = get_bloginfo( 'name' );
$blog_title_formatted = str_replace( ' ', '-', strtolower( $blog_title ) );

// Today's DateTime
$datetime_now = date( 'Y-m-d_H-i-s' );

// get data
$gl_customers_table = new GL_Customers_Table;
$customer_data = $gl_customers_table->get_customers();

// BUILD SPREADSHEET
// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator( 'Gineico Lighting' )
    ->setLastModifiedBy( 'Gineico Lighting' )
    ->setTitle( $blog_title . ' - Members List' . $title_info . 'Date: ' . $datetime_now )
    ->setSubject( $blog_title . ' - Members List' )
    ->setDescription( 'Excel report of ' . $blog_title . ' members according to the following filters: ' . $title_info )
    ->setKeywords( strtolower( $blog_title ) . ' members export' );
    //->setCategory('Test result file');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
// Get Active Sheet
$active_sheet = $spreadsheet->getActiveSheet();

// Add Headings
$active_sheet->setCellValue('A1', 'Username');
$active_sheet->setCellValue('B1', 'Email');
$active_sheet->setCellValue('C1', 'First Name');
$active_sheet->setCellValue('D1', 'Last Name');
$active_sheet->setCellValue('E1', 'Company');
$active_sheet->setCellValue('F1', 'Registered');

// Add Data
// set starting row
$sheet_row = 2;

foreach( $customer_data as $customer ) {

    // Add Data
    // set value binder
    \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );

    $active_sheet->setCellValue( 'A' . $sheet_row, html_entity_decode( $customer['user_name'] ) );

    $active_sheet->setCellValue( 'B'. $sheet_row, html_entity_decode( $customer['email'] ) );

    $active_sheet->setCellValue( 'C' . $sheet_row, $customer['first_name'] );
    $active_sheet->setCellValue( 'D' . $sheet_row, $customer['last_name'] );
    $active_sheet->setCellValue( 'E' . $sheet_row, $customer['company'] );

    $active_sheet->setCellValue( 'D' . $sheet_row, $customer['registered_date'] );
    // format date
    $active_sheet->getStyle( 'F' . $sheet_row )
        ->getNumberFormat()
        ->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY );



    // increment sheet row
    $sheet_row++;
}

// Set Vertical Alignment to Top
$active_sheet->getStyle( 'A1:F' . $sheet_row )->getAlignment()->setVertical( \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP );
// set top row to bold
$active_sheet->getStyle( 'A1:F1' )->getFont()->setBold( true );
// Set Column Widths
$active_sheet->getColumnDimension('A')->setAutoSize(true);
$active_sheet->getColumnDimension('B')->setAutoSize(true);
$active_sheet->getColumnDimension('C')->setAutoSize(true);
$active_sheet->getColumnDimension('D')->setAutoSize(true);
$active_sheet->getColumnDimension('E')->setAutoSize(true);
$active_sheet->getColumnDimension('F')->setAutoSize(true);

// Rename worksheet
$active_sheet->setTitle('Customers ');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $blog_title_formatted . '-members' . ( str_replace( " ", "_", trim( $title_info ) ) ) . '_' . $datetime_now . '.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
