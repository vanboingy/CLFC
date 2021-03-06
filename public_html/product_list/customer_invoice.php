<?php
include_once ('func.get_baskets_list.php');
valid_auth('member');

// Set content_top to show basket selector...
$content_top .= get_baskets_list ();

// Do not paginate invoices under any circumstances (web pages)
$per_page = 1000000;

// Do not show search on non-shopping pages
$show_search = false;

// SELECT is done against the ledger instead of the basket so we can pick
// up any adjustments as well...
$where_misc = '
    (('.NEW_TABLE_LEDGER.'.source_type="member" AND '.NEW_TABLE_LEDGER.'.source_key="'.mysql_real_escape_string($member_id).'")
      OR ('.NEW_TABLE_LEDGER.'.target_type="member" AND '.NEW_TABLE_LEDGER.'.target_key="'.mysql_real_escape_string($member_id).'"))
    AND '.NEW_TABLE_LEDGER.'.delivery_id="'.mysql_real_escape_string($delivery_id).'"
    AND '.NEW_TABLE_LEDGER.'.replaced_by IS NULL';

$order_by = '
    '.TABLE_CATEGORY.'.sort_order ASC,
    '.TABLE_PRODUCER.'.business_name ASC,
    '.NEW_TABLE_PRODUCTS.'.product_name ASC,
    '.NEW_TABLE_PRODUCTS.'.unit_price ASC';

// Assign page tab and title information
$page_title_html = '<span class="title">Basket</span>';
$page_subtitle_html = '<span class="subtitle">Basket Items</span>';
$page_title = 'Basket: Basket Items';
$page_tab = 'shopping_panel';

// Set display groupings
$major_division = 'producer_id';
$major_division_prior = $major_division.'_prior';
$minor_division = 'subcategory_id';
$minor_division_prior = $minor_division.'_prior';
$show_major_division = true;
$show_minor_division = false;
$row_type = 'product'; // Reflects the detail to show on each row (vs. what gets featured in the header)

// Assign template file
$template_type = 'customer_invoice';

// Execute the main product_list query
$query = '
  SELECT
    SQL_CALC_FOUND_ROWS
    '.NEW_TABLE_BASKET_ITEMS.'.quantity AS basket_quantity,
    '.NEW_TABLE_BASKET_ITEMS.'.total_weight,
    '.NEW_TABLE_BASKET_ITEMS.'.product_fee_percent,
    '.NEW_TABLE_BASKET_ITEMS.'.subcategory_fee_percent,
    '.NEW_TABLE_BASKET_ITEMS.'.producer_fee_percent,
    '.NEW_TABLE_BASKET_ITEMS.'.out_of_stock,
    '.NEW_TABLE_BASKET_ITEMS.'.checked_out,
    COUNT('.NEW_TABLE_BASKET_ITEMS.'.bpid) AS number_of_products,
    '.NEW_TABLE_LEDGER.'.amount,
    '.NEW_TABLE_LEDGER.'.text_key,
    '.TABLE_MEMBER.'.address_line1,
    '.TABLE_MEMBER.'.address_line2,
    '.TABLE_MEMBER.'.auth_type,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.city,
    '.TABLE_MEMBER.'.state,
    '.TABLE_MEMBER.'.zip,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.email_address_2,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.preferred_name,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_MEMBER.'.mobile_phone,
    '.TABLE_MEMBER.'.work_phone,
    '.TABLE_MEMBER.'.fax,
    '.TABLE_MEMBER.'.work_address_line1,
    '.TABLE_MEMBER.'.work_address_line2,
    '.TABLE_MEMBER.'.work_city,
    '.TABLE_MEMBER.'.work_state,
    '.TABLE_MEMBER.'.work_zip,
    '.TABLE_DELCODE.'.hub,
    '.TABLE_DELCODE.'.deltype,
    '.TABLE_DELCODE.'.truck_code,
    '.TABLE_DELCODE.'.delcode,
    '.TABLE_DELCODE.'.deldesc,
    '.NEW_TABLE_BASKETS.'.delcode_id,
    '.NEW_TABLE_BASKETS.'.basket_id,
    '.NEW_TABLE_BASKETS.'.member_id,
    '.NEW_TABLE_BASKETS.'.delivery_id,
    '.NEW_TABLE_BASKETS.'.delivery_postal_code,
    '.NEW_TABLE_BASKETS.'.customer_fee_percent,
    '.NEW_TABLE_PRODUCTS.'.product_id,
    '.NEW_TABLE_PRODUCTS.'.product_version,
    '.NEW_TABLE_PRODUCTS.'.product_name,
    '.NEW_TABLE_PRODUCTS.'.inventory_pull,
    '.NEW_TABLE_PRODUCTS.'.inventory_id,
    '.NEW_TABLE_PRODUCTS.'.product_description,
    '.NEW_TABLE_PRODUCTS.'.unit_price,
    '.NEW_TABLE_PRODUCTS.'.pricing_unit,
    '.NEW_TABLE_PRODUCTS.'.ordering_unit,
    '.NEW_TABLE_PRODUCTS.'.random_weight,
    '.NEW_TABLE_PRODUCTS.'.meat_weight_type,
    '.NEW_TABLE_PRODUCTS.'.minimum_weight,
    '.NEW_TABLE_PRODUCTS.'.maximum_weight,
    '.NEW_TABLE_PRODUCTS.'.extra_charge,
    '.NEW_TABLE_PRODUCTS.'.image_id,
    '.NEW_TABLE_PRODUCTS.'.listing_auth_type,
    '.NEW_TABLE_PRODUCTS.'.taxable,
    '.NEW_TABLE_PRODUCTS.'.tangible,
    '.NEW_TABLE_PRODUCTS.'.sticky,
    '.NEW_TABLE_PRODUCTS.'.confirmed,
    '.TABLE_CATEGORY.'.category_name,
    '.TABLE_CATEGORY.'.sort_order,
    '.TABLE_SUBCATEGORY.'.subcategory_name,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.business_name AS producer_name,
    '.TABLE_PRODUCT_TYPES.'.prodtype,
    '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_type,
    '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_code,
    '.TABLE_ORDER_CYCLES.'.delivery_date,
    '.TABLE_ORDER_CYCLES.'.msg_all,
    '.TABLE_ORDER_CYCLES.'.msg_bottom,
    '.NEW_TABLE_MESSAGES.'.message AS customer_message
  FROM
    '.NEW_TABLE_LEDGER.'
  LEFT JOIN '.NEW_TABLE_PRODUCTS.' USING(pvid)
  LEFT JOIN '.NEW_TABLE_BASKET_ITEMS.' ON ('.NEW_TABLE_PRODUCTS.'.product_id = '.NEW_TABLE_BASKET_ITEMS.'.product_id AND '.NEW_TABLE_PRODUCTS.'.product_version = '.NEW_TABLE_BASKET_ITEMS.'.product_version)
  LEFT JOIN '.NEW_TABLE_BASKETS.' ON '.NEW_TABLE_BASKET_ITEMS.'.basket_id = '.NEW_TABLE_BASKETS.'.basket_id
  LEFT JOIN '.TABLE_MEMBER.' USING(member_id)
  LEFT JOIN '.TABLE_DELCODE.' ON '.NEW_TABLE_BASKETS.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
  LEFT JOIN '.TABLE_PRODUCER.' ON '.TABLE_PRODUCER.'.producer_id = '.NEW_TABLE_PRODUCTS.'.producer_id
  LEFT JOIN '.TABLE_SUBCATEGORY.' ON '.TABLE_SUBCATEGORY.'.subcategory_id = '.NEW_TABLE_PRODUCTS.'.subcategory_id
  LEFT JOIN '.TABLE_CATEGORY.' ON '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
  LEFT JOIN '.TABLE_PRODUCT_TYPES.' ON '.TABLE_PRODUCT_TYPES.'.production_type_id = '.NEW_TABLE_PRODUCTS.'.production_type_id
  LEFT JOIN '.TABLE_PRODUCT_STORAGE_TYPES.' ON '.NEW_TABLE_PRODUCTS.'.storage_id = '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_id
  LEFT JOIN '.TABLE_ORDER_CYCLES.' ON '.NEW_TABLE_BASKETS.'.delivery_id = '.TABLE_ORDER_CYCLES.'.delivery_id
  LEFT JOIN '.NEW_TABLE_MESSAGES.' ON
    ( referenced_key1 = '.NEW_TABLE_BASKET_ITEMS.'.bpid
    AND message_type_id =
      (SELECT message_type_id FROM '.NEW_TABLE_MESSAGE_TYPES.' WHERE description = "customer notes to producer")
    )
  WHERE'.
    // $where_producer_pending.
    // $where_unlisted_producer.
    $where_misc.
    // $where_zero_inventory.
    // $where_confirmed.
    // $where_auth_type.
    '
  GROUP BY transaction_id
  ORDER BY'.
    $order_by;
?>