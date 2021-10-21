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
// Get membership product Title
$title_info = '';

// Today's DateTime
$datetime_now = date( 'Y-m-d_H-i-s' );

// get data
$pet_names_data = HFPSP_Pet_Names_List::hfpsp_get_pet_names_data();

// BUILD SPREADSHEET
// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator( 'Harmony Fund' )
    ->setLastModifiedBy( 'Harmony Fund' )
    ->setTitle( $blog_title . ' - Pet Names List' . $title_info . 'Date: ' . $datetime_now )
    ->setSubject( $blog_title . ' - Pet Names List' )
    ->setDescription( 'Excel report of ' . $blog_title . ' Pet Names' )
    ->setKeywords( strtolower( $blog_title ) . ' pet names export' );
    //->setCategory('Test result file');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
// Get Active Sheet
$active_sheet = $spreadsheet->getActiveSheet();

// Add Headings
$active_sheet->setCellValue('A1', 'First Name');
$active_sheet->setCellValue('B1', 'Last Name');
$active_sheet->setCellValue('C1', 'Created Date');
$active_sheet->setCellValue('D1', 'Donor');
$active_sheet->setCellValue('E1', 'Email');
$active_sheet->setCellValue('F1', 'Registration Date');
// PWD TEST BEGIN
//$active_sheet->setCellValue('I1', urldecode($_SERVER['REQUEST_URI']));
// PWD TEST END

// Add Data
// set starting row
$sheet_row = 2;

foreach( $pet_names_data as $pet_name ) {

    // Add Data
    // set value binder
    \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );

    // Pet first name
    $active_sheet->setCellValue( 'A' . $sheet_row, html_entity_decode( $pet_name['first_name'] ) );

    // Pet last name
    $active_sheet->setCellValue( 'B'. $sheet_row, html_entity_decode( $pet_name['last_name'] ) );

    // Pet profile creation date
    $active_sheet->setCellValue( 'C' . $sheet_row, $pet_name['created_date'] );

    // Donor name
    $active_sheet->setCellValue( 'D' . $sheet_row, $pet_name['donor'] );

    // Donor email
    $active_sheet->setCellValue( 'E' . $sheet_row, $pet_name['donor_email'] );

    // Donor registration date
    $active_sheet->setCellValue( 'F' . $sheet_row, $pet_name['registration_date'] );

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
$active_sheet->setTitle('Pet Names ');

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
