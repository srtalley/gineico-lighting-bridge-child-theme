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

// Get Filters
$membership_filter = (int) filter_input( INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT );
$newsletter_filter = filter_input( INPUT_GET, 'product', FILTER_SANITIZE_STRING );
// Get Website Title
$blog_title = get_bloginfo( 'name' );
$blog_title_formatted = str_replace( ' ', '-', strtolower( $blog_title ) );
// Get membership product Title
$title_info = '';
if( $membership_filter ) {
    $product = wc_get_product( $membership_filter );
    $title_info = ' (' . $product->get_title() . ')';
}
// Today's DateTime
$datetime_now = date( 'Y-m-d_H-i-s' );

// get data
$members_data = GL_Customers_List::hfpsp_get_members_data( $membership_filter, $newsletter_filter );

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
$active_sheet->setCellValue('A1', 'Name');
$active_sheet->setCellValue('B1', 'Memberships');
$active_sheet->setCellValue('C1', 'Count');
$active_sheet->setCellValue('D1', 'Start Date');
$active_sheet->setCellValue('E1', 'Phone');
$active_sheet->setCellValue('F1', 'Email');
$active_sheet->setCellValue('G1', 'Address');

// PWD TEST BEGIN
//$active_sheet->setCellValue('I1', urldecode($_SERVER['REQUEST_URI']));
// PWD TEST END

// Add Data
// set starting row
$sheet_row = 2;

foreach( $members_data as $member ) {

    // Add Data
    // set value binder
    \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );

    // Customer Name
    $active_sheet->setCellValue( 'A' . $sheet_row, html_entity_decode( $member['name'] ) );

    // Memberships
    $active_sheet->setCellValue( 'B'. $sheet_row, html_entity_decode( $member['memberships'] ) );

    //Count
    $active_sheet->setCellValue( 'C' . $sheet_row, $member['count'] );

    // Start Date
    $active_sheet->setCellValue( 'D' . $sheet_row, $member['start_date'] );
    // format date
    $active_sheet->getStyle( 'D' . $sheet_row )
        ->getNumberFormat()
        ->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY );

    // Customer Phone
    // Set cell with a numeric value, but tell PhpSpreadsheet it should be treated as a string
    $active_sheet->setCellValueExplicit(
        'E' . $sheet_row,
        $member['phone'],
        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
    );

    // Customer Email
    $active_sheet->setCellValue( 'F' . $sheet_row, $member['email'] );

    // Customer Address
    $active_sheet->setCellValue( 'G' . $sheet_row, $member['address'] );

    // increment sheet row
    $sheet_row++;
}

// Set Vertical Alignment to Top
$active_sheet->getStyle( 'A1:G' . $sheet_row )->getAlignment()->setVertical( \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP );
// set top row to bold
$active_sheet->getStyle( 'A1:G1' )->getFont()->setBold( true );
// Set Column Widths
$active_sheet->getColumnDimension('A')->setAutoSize(true);
$active_sheet->getColumnDimension('B')->setAutoSize(true);
$active_sheet->getColumnDimension('C')->setAutoSize(true);
$active_sheet->getColumnDimension('D')->setAutoSize(true);
$active_sheet->getColumnDimension('E')->setAutoSize(true);
$active_sheet->getColumnDimension('F')->setAutoSize(true);
$active_sheet->getColumnDimension('G')->setAutoSize(true);

// Rename worksheet
$active_sheet->setTitle('Members ');

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
