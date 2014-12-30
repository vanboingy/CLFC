<?php
include_once ("config_foodcoop.php");
include_once ("general_functions.php");
session_start();
valid_auth('producer,producer_admin,site_admin,cashier');


$producer_id = $_SESSION['producer_id_you'];
// cashier and site_admin are allowed to view producer invoices by $_GET directive
if ($_GET['producer_id'] && (CurrentMember::auth_type('site_admin') || CurrentMember::auth_type('cashier')))
  {
    $producer_id = $_GET['producer_id'];
  }

if ( $_GET['delivery_id'] )
  {
    $delivery_id = $_GET['delivery_id'];
  }
else
  {
    $delivery_id = ActiveCycle::delivery_id();
  }

if ($_GET['display_only'] == "true")
  {
    $display_only = true;
  }
else
  {
    $display_only = false;
  }

if ( $_POST['updatevalues'] == "ys" && $_POST['product_id'] && $_POST['c_basket_id'])
  {
    $sqlu = '
      UPDATE
        '.TABLE_BASKET.'
      SET
        total_weight = "'.mysql_real_escape_string ($_POST['total_weight']).'",
        out_of_stock = "'.mysql_real_escape_string ($_POST['out_of_stock']).'"
      WHERE
        basket_id = '.mysql_real_escape_string ($_POST['c_basket_id']).'
        AND product_id = '.$_POST['product_id'];
    $result = @mysql_query($sqlu, $connection) or die(mysql_error());
    $message2 = '<b><font color="#3333FF">The information has been updated.</font></b><br><br>';
  }

// Get the delivery markup/down information
$query = '
  SELECT
    delivery_date,
    producer_markdown,
    wholesale_markup,
    retail_markup
  FROM
    '.TABLE_ORDER_CYCLES.'
  WHERE
    delivery_id = "'.mysql_real_escape_string ($delivery_id).'"';
$result = @mysql_query($query, $connection) or die(mysql_error());
if ( $row = mysql_fetch_array($result) )
  {
    $delivery_date = date ("F j, Y", strtotime ($row['delivery_date']));
    $producer_markdown = $row['producer_markdown'] / 100;
    $retail_markup = $row['retail_markup'] / 100;
    $wholesale_markup = $row['wholesale_markup'] / 100;
  }

$total = 0;
$total_pr = 0;
$subtotal_pr = 0;

include('../func/producer_orders_bycustomerstorage.php');
include ('../func/producer_orders_totals.php');
include("../func/show_businessname.php");





if ($_GET['output'] == 'pdf')
  {
    $fp = fopen( FILE_PATH.PATH.'members/temp/producer_invoice_temp.html', a);
    fwrite($fp, $producer_orders_bycustomerstorage);
    fwrite($fp, $producer_orders_totals);
    // Now convert to PDF and send to browser
    sleep (3);
    putenv("HTMLDOC_NOCGI=1");
    header("Content-Type: application/pdf");
    flush();
    passthru('htmldoc -t pdf --webpage '.FILE_PATH.PATH.'members/temp/producer_invoice_temp.html');
    unlink(FILE_PATH.PATH.'members/temp/producer_invoice_temp.html');
  }
elseif ($display_only)
  {
    echo '
    '.$producer_orders_bycustomerstorage.'
    '.$producer_orders_totals;
  }
else
  {
    $page_title_html = '<span class="title">'.$business_name.'</span>';
    $page_subtitle_html = '<span class="subtitle">Producer Invoice (by customer/storage)</span>';
    $page_title = ''.$business_name.': Producer Invoice (by customer/storage)';
    $page_tab = 'producer_admin_panel';

    include("template_header.php");
    echo '
      <!-- CONTENT BEGINS HERE -->
        <div align="center">
          <h3>Producer List for '.$delivery_date.' for '.$a_business_name.'</h3>
          '.$message.'
        </div>
        '.$producer_orders_bycustomerstorage.'
        '.$producer_orders_totals.'
        </div>
      <!-- CONTENT ENDS HERE -->';
    include("template_footer.php");
  }
