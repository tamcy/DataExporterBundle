<?php

// !!! WARNING !!!
// This is merely a sample script to demonstrate how to use the library
// to export data to Google Spreadsheet. As this code has access to the
// Google Drive account you provide, you must make sure you know what
// is happening behind the scene. I am not responsible for any loss of
// data caused by running this script.

require '../vendor/autoload.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\Exporter;
use Sparkson\DataExporterBundle\Exporter\OutputAdapter\GoogleSpreadsheetAdapter;

// Working example of the GoogleSpreadsheetOutputAdapter class.
// Usage:
// 1. Create an empty spreadsheet file named "MyTestSheet" in your Google Drive.
// 2. Go to Google's OAuth 2.0 Playground (https://developers.google.com/oauthplayground).
// 3. At step 1, select "https://www.googleapis.com/auth/drive" from the list (under Drive API v2).
// 4. Enter "https://spreadsheets.google.com/feeds" in the "scope" text box.
// 5. Click "Authorize APIs" and grant privileges.
// 6. At step 2, click "exchange authorization code" and get the "Access token".
// 7. Copy the access token to the variable below.

$accessToken = 'YOUR_ACCESS_TOKEN';

// 8. Run this file and see the sample data exported to "MyTestSheet".

// =========================================================================================

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);

$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheetFeed = $spreadsheetService->getSpreadsheets();

$spreadsheet = null;
// Find "MyTestSheet"
foreach ($spreadsheetFeed as $iSpreadsheet) {
    /** @var \Google\Spreadsheet\Spreadsheet $iSpreadsheet */
    if ($iSpreadsheet->getTitle() == 'MyTestSheet') {
        $spreadsheet = $iSpreadsheet;
        break;
    }
}

if (!$spreadsheet) {
    die('Sheet "MyTestSheet" not found!');
}

$worksheets = $spreadsheet->getWorksheets();
$worksheet = $worksheets[0];

// ========================================================

$dataSet = array(
    array('firstName' => 'Foo', 'lastName' => 'Chan', 'age' => 10),
    array('firstName' => 'Bar', 'lastName' => 'Wong', 'age' => 15),
);

// ========================================================


$outputAdapter = new GoogleSpreadsheetAdapter($worksheet);
$columns = new ColumnSet();
$columns->addChild(new Column('firstName', new StringType(), array('property_path' => '[firstName]')));
$columns->addChild(new Column('lastName', new StringType(), array('property_path' => '[lastName]')));
$columns->addChild(new Column('age', new StringType(), array('property_path' => '[age]')));

$exporter = new Exporter();
$exporter
    ->setColumns($columns)
    ->setOutputAdapter($outputAdapter)
    ->setDataSet($dataSet)
    ->execute();
