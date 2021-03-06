<?php
include_once ('config_foodcoop.php');

function prdcr_info ($producer_id, $producer_link)
  {
    global $connection;
//echo "ID:$producer_id -- LINK:$producer_link<br />";
/*    $sqlr = '
      SELECT
        '.TABLE_PRODUCER.'.*,
        '.TABLE_MEMBER.'.*
      FROM
        '.TABLE_PRODUCER.'
      LEFT JOIN '.TABLE_MEMBER.' USING(member_id)
      WHERE
        ('.TABLE_PRODUCER.'.producer_id = "'.mysql_real_escape_string ($producer_id).'"
          OR '.TABLE_PRODUCER.'.producer_link = "'.mysql_real_escape_string ($producer_link).'")
        AND '.TABLE_PRODUCER.'.pending = 0
        AND '.TABLE_PRODUCER.'.unlisted_producer != 2
      ORDER BY
        '.TABLE_MEMBER.'.business_name ASC';
*/
    $sqlr = '
      SELECT
        '.TABLE_PRODUCER.'.*,
        '.TABLE_MEMBER.'.*
      FROM
        '.TABLE_PRODUCER.'
      LEFT JOIN '.TABLE_MEMBER.' USING(member_id)
      WHERE
        ('.TABLE_PRODUCER.'.producer_id = "'.mysql_real_escape_string ($producer_id).'")
        AND '.TABLE_PRODUCER.'.pending = 0
        AND '.TABLE_PRODUCER.'.unlisted_producer != 2
      ORDER BY
        '.TABLE_MEMBER.'.business_name ASC';
$rsr = @mysql_query($sqlr, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($rsr) )
      {
        $producer_id = $row['producer_id'];
        $business_name =  $row['business_name'];
        $producttypes =  $row['producttypes'];
        $about =  $row['about'];
        $general_practices =  $row['general_practices'];
        $ingredients =  $row['ingredients'];
        $highlights =  $row['highlights'];
        $additional = $row['additional'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $first_name_2 = $row['first_name_2'];
        $last_name_2 = $row['last_name_2'];
        $address_line1 = $row['address_line1'];
        $address_line2 = $row['address_line2'];
        $city = $row['city'];
        $state = $row['state'];
        $zip = $row['zip'];
        $email_address = $row['email_address'];
        $email_address_2 = $row['email_address_2'];
        $home_phone = $row['home_phone'];
        $work_phone = $row['work_phone'];
        $mobile_phone = $row['mobile_phone'];
        $fax = $row['fax'];
        $toll_free = $row['toll_free'];
        $home_page = $row['home_page'];
        $membership_date = $row['membership_date'];
        $pub_address = $row['pub_address'];
        $pub_email = $row['pub_email'];
        $pub_email2 = $row['pub_email2'];
        $pub_phoneh = $row['pub_phoneh'];
        $pub_phonew = $row['pub_phonew'];
        $pub_phonec = $row['pub_phonec'];
        $pub_phonet = $row['pub_phonet'];
        $pub_fax = $row['pub_fax'];
        $pub_web = $row['pub_web'];
        $display_logo = '';
        $sqll = '
          SELECT
            '.TABLE_PRODUCER_LOGOS.'.*
          FROM
            '.TABLE_PRODUCER_LOGOS.'
          WHERE
            '.TABLE_PRODUCER_LOGOS.'.producer_id = "'.mysql_real_escape_string ($producer_id).'"';
        $rsrl = @mysql_query($sqll, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
        while ( $row = mysql_fetch_array($rsrl) )
          {
            $logo_id = $row['logo_id'];
            $logo_desc = $row['logo_desc'];
            if ( $logo_id )
              {
              	// IMAGE_DISPLAY_CALC
function image_display_calc2($data)
  { return
    ($data['logo_id'] ?
    '<img src="getlogos.php?logo_id='.$data['logo_id'].'" width="150" hspace="5" alt="'.$data['logo_desc'].'" /">'
    : '');
  };

                $display_logo = '<td width="150" align="left">
                  '.image_display_calc2($row);
              }
          }
        $display .= '
          <div align="right"><a href="prdcr_list.php">Back to Producers List</a></div>
          <table align="center" width="95%" cellpadding="10" cellspacing="2" border="1" bordercolor="#000000" bgcolor="#ffffff">
            <tr><td bgcolor="#DDDDDD" align="left">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#DDDDDD">
              <tr>
                '.$display_logo.'
              <td align="left">
            <font face="arial" size="4">
            '.$business_name.'<br><br>'.$city.', '.$state.'
            </font><br>
              </td><td align="right">'.$_GLOBALS['font'];

        if (PRDCR_INFO_PUBLIC || ($_SESSION['member_id']))
          {
            if ( $address_line1 && $pub_address )
              {
                $display .= $address_line1.'<br>';
              }
            if ( $address_line2 && $pub_address )
              {
                $display .= $address_line2.'<br>';
              }
            if ( $address_line1 && $pub_address )
              {
                $display .= "$city, $state $zip<br>";
              }
            if ( $email_address && $pub_email )
              {
                $display .= '<a href="mailto:'.$email_address.'">'.$email_address.'</a><br>';
              }
            if ($email_address_2 && $pub_email2)
              {
                $display .= '<a href="mailto:'.$email_address_2.'">'.$email_address_2.'</a><br>';
              }
            if ( $home_phone && $pub_phoneh )
              {
                $display .= $home_phone .' (home)<br>';
              }
            if ( $work_phone && $pub_phonew )
              {
                $display .= $work_phone .' (work)<br>';
              }
            if ( $mobile_phone && $pub_phonec )
              {
                $display .= $mobile_phone .' (cell)<br>';
              }
            if ( $fax && $pub_fax )
              {
                $display .= $fax .'(fax)<br>';
              }
            if ( $toll_free && $pub_phonet )
              {
                $display .= $toll_free .' (toll free)<br>';
              }
            if ( $home_page && $pub_web )
              {
                $display .= '<a href="http://'.$home_page.'" target="_blank">'.$home_page.'</a><br>';
              }
          }
        $display .= '
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td align="left">';
        if ( $producttypes )
          {
            $display .= '
            <font face="arial" size="3"><b>Product Types: </b></font>
            <font face="arial" size="-1">'.$producttypes.'</font><br><br>';
          }
        if ( $about )
          {
            $display .= '
            <font face="arial" size="3"><b>About Us</b></font><br>
            <font face="arial" size="-1">'.$about.'</font><br><br>';
          }
        if ( $ingredients )
          {
            $display .= '
            <font face="arial" size="3"><b>Ingredients</b></font><br>
            <font face="arial" size="-1">'.$ingredients.'</font><br><br>';
           }
        if ( $general_practices )
          {
            $display .= '
            <font face="arial" size="3"><b>Practices (our standards for raising or making our products)</b></font><br>
              <font face="arial" size="-1">'.$general_practices.'</font><br><br>';
          }
        if ( $additional )
          {
            $display .= '
            <font face="arial" size="3"><b>Additional Information</b></font><br>
            <font face="arial" size="-1">'.$additional.'</font><br><br>';
          }
        if ( $highlights )
          {
            $display .= '
            <font face="arial" size="3"><b>Highlights this Month</b></font><br>
            <font face="arial" size="-1">'.$highlights.'</font>';
          }
//        $display .= '<br><div align="right"><a href="'.PATH.'prdcr_display_quest.php?pid='.$producer_id.'" target="_blank">Learn more about this producer.</a></div>';
        $display .= '</td></tr></table>';
      }
    return $display;
  }
?>
