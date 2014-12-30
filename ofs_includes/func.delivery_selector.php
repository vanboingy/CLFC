<?php

// This function will get the html markup for a div containing formatted basket history.
// Sample/suggested CSS is given at the end.
function delivery_selector ($current_delivery_id)
  {
    global $connection;
    // Get a list of the order cycles in reverse order
    $delivery_id_array = array();
    $delivery_attrib = array ();
    $query = '
      SELECT 
        delivery_id,
        date_open,
        date_closed,
        order_fill_deadline,
        delivery_date
      FROM
        '.TABLE_ORDER_CYCLES.'
      WHERE
        date_open < NOW()
      ORDER BY
        delivery_date DESC';
    $result = @mysql_query($query, $connection) or die(debug_print ("ERROR: 898034 ", array ($query,mysql_error()), basename(__FILE__).' LINE '.__LINE__));
    WHILE ($row = mysql_fetch_array($result))
      {
        array_push ($delivery_id_array, $row['delivery_id']);
        $delivery_attrib[$row['delivery_id']]['date_open'] = $row['date_open'];
        $delivery_attrib[$row['delivery_id']]['time_open'] = strtotime($row['date_open']);
        $delivery_attrib[$row['delivery_id']]['date_closed'] = $row['date_closed'];
        $delivery_attrib[$row['delivery_id']]['time_closed'] = strtotime($row['date_closed']);
        $delivery_attrib[$row['delivery_id']]['order_fill_deadline'] = $row['order_fill_deadline'];
        $delivery_attrib[$row['delivery_id']]['delivery_date'] = $row['delivery_date'];
      }
    // Now get this customer's baskets
    $list_title = 'Select Delivery Date';
    foreach ($delivery_id_array as $delivery_id)
      {
        // Check if this is the current delivery
        if ($delivery_id == $current_delivery_id)
          {
            $current = true;
            $list_title = 'Selected: '.date('M j, Y', strtotime($delivery_attrib[$delivery_id]['delivery_date']));
          }
        else
          {
            $current = false;
          }
        $day_open = date ('j', $delivery_attrib[$delivery_id]['time_open']);
        $month_open = date ('M', $delivery_attrib[$delivery_id]['time_open']);
        $year_open = date ('Y', $delivery_attrib[$delivery_id]['time_open']);
        $day_closed = date ('j', $delivery_attrib[$delivery_id]['time_closed']);
        $month_closed = date ('M', $delivery_attrib[$delivery_id]['time_closed']);
        $year_closed = date ('Y', $delivery_attrib[$delivery_id]['time_closed']);
        if ($day_open == $day_closed) $day_open = '';
        if ($month_open == $month_closed) $month_closed = '';
        if ($year_open == $year_closed) $year_open = '';
        $items_in_basket = abs($delivery_attrib[$delivery_id]['checked_out']);
// Need some onclick code for class=view (full baskets)
        $list_display .= '
          <li class="view'.($current == true ? ' current' : '').'">
            <span class="delivery_date">Delivery: '.date('M j, Y', strtotime($delivery_attrib[$delivery_id]['delivery_date'])).'</span>
            <span class="order_dates">'.$month_open.' '.$day_open.' '.$year_open.' &ndash; '.$month_closed.' '.$day_closed.' '.$year_closed.'</span>
            <span class="basket_qty">'.$basket_quantity_text.'</span>
            <span class="basket_action"><a href="'.$_SERVER['PHP_SELF'].'?delivery_id='.$delivery_id.'">Jump to this delivery</a></span>
          </li>';
      }
    // Display the order cycles and baskets...
    $display .= '
        <div id="basket_dropdown" class="dropdown" onclick="$(this).toggleClass(\'clicked\')">
          <h1 class="cycle_history">
            '.$list_title.'
          </h1>
          <div id="cycle_history">
            <ul class="cycle_history">'.
              $list_display.'
            </ul>
          </div>
        </div>';
    return $display;
  }

// /* Styles for the all dropdowns */
//   .dropdown {
//     -transition:height 0.7s cubic-bezier(1,0,0.5,1);
//     -webkit-transition:height 0.7s cubic-bezier(1,0,0.5,1);
//     -moz-transition:height 0.7s cubic-bezier(1,0,0.5,1);
//     -o-transition:height 0.7s cubic-bezier(1,0,0.5,1);
//     -ie-transition:height 0.7s cubic-bezier(1,0,0.5,1);
//     }
// /* Styles for the Basket Selector */
//   #basket_dropdown {
//     border:1px solid #000;
//     float:left;
//     width:300px;
//     height:26px;
//     overflow:hidden;
//     }
//   #basket_dropdown:hover {
//     height:400px;
//     }
//   h1.cycle_history {
//     width:294px;
//     font-size:16px;
//     color:#efe;
//     background-color:#050;
//     position:relative;
//     height:20px;
//     margin:0;
//     padding:3px;
//     }
//   #cycle_history {
//     width:100%;
//     background-color:#fff;
//     overflow:auto;
//     width:300px;
//     height:374px;
//     }
//   ul.cycle_history {
//     list-style-type:none;
//     padding-left:0;
//     }
//   ul.cycle_history li {
//     padding-top:10px;
//     text-align:left;
//     height:55px;
//     padding-left:70px;
//     border-top:1px solid transparent;
//     border-bottom:1px solid transparent;
//     }
//   ul.cycle_history li:hover {
//     }
//   ul.cycle_history li.view:hover {
//     cursor:pointer;
//     background-color:#efd;
//     border-top:1px solid #ad6;
//     border-bottom:1px solid #ad6;
//     }
//   ul.cycle_history li span {
//     vertical-align: middle;
//     }
//   .delivery_date {
//     display:block;
//     font-size:130%;
//     font-weight:bold;
//     color:#350;
//     }
//   .order_dates {
//     display:block;
//     font-size:90%;
//     color:#530;
//     }
//   .basket_qty {
//     color:#000;
//     }
//   li.fcs {
//     background:url(../grfx/basket-fcs.png) no-repeat 7px 7px;
//     }
//   li.fci {
//     background:url(../grfx/basket-fci.png) no-repeat 7px 7px;
//     }
//   li.fgs {
//     background:url(../grfx/basket-fgs.png) no-repeat 7px 7px;
//     }
//   li.fgi {
//     background:url(../grfx/basket-fgi.png) no-repeat 7px 7px;
//     }
//   li.ecs {
//     background:url(../grfx/basket-ecs.png) no-repeat 7px 7px;
//     }
//   li.eci {
//     background:url(../grfx/basket-eci.png) no-repeat 7px 7px;
//     }
//   li.egs {
//     background:url(../grfx/basket-egs.png) no-repeat 7px 7px;
//     }
//   li.egi {
//     background:url(../grfx/basket-egi.png) no-repeat 7px 7px;
//     }
//   li.eci span,
//   li.egs span,
//   li.egi span {
//     color:#999;
//     }
?>