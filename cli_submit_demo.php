<?php
// CLI end-to-end test: creates an archive with banner + PDF, exercising S3 paths
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/classes/Master.php');

// Ensure session and minimal user context
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['userdata'])) $_SESSION['userdata'] = [];
$_SESSION['userdata']['id'] = $_SESSION['userdata']['id'] ?? 1;
$_SESSION['userdata']['curriculum_id'] = $_SESSION['userdata']['curriculum_id'] ?? 1;

// Prepare POST fields
$_POST = [
  'title' => 'CLI Test Submission',
  'year' => date('Y'),
  'abstract' => 'Test abstract for CLI submission.',
  'members' => '<ul><li>Alice</li><li>Bob</li></ul>'
];

// Create temporary banner PNG without GD (1x1 pixel)
$tmpPng = tempnam(sys_get_temp_dir(), 'cli_png_');
$pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO3f1/8AAAAASUVORK5CYII=';
file_put_contents($tmpPng, base64_decode($pngBase64));

// Create temporary PDF (minimal valid structure)
$tmpPdf = tempnam(sys_get_temp_dir(), 'cli_pdf_');
$pdfContent = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n1 0 obj<<>>endobj\n2 0 obj<<>>endobj\n3 0 obj<</Type/Catalog/Pages 4 0 R>>endobj\n4 0 obj<</Type/Pages/Count 0>>endobj\ntrailer<</Root 3 0 R>>\n%%EOF";
file_put_contents($tmpPdf, $pdfContent);

// Populate $_FILES as if from a real upload
$_FILES = [
  'pdf' => [
    'name' => 'test-doc.pdf',
    'type' => 'application/pdf',
    'tmp_name' => $tmpPdf,
    'error' => 0,
    'size' => filesize($tmpPdf)
  ]
];

$master = new Master();
$result = $master->save_archive();
echo $result; // JSON response

// Cleanup temp files
@unlink($tmpPng);
@unlink($tmpPdf);
?>