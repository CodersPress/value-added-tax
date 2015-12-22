<?php
/*
Plugin Name: Value Added Tax
Plugin URI: http://coderspress.com/
Description: Adds VAT - Invoices template
Version: 1.0.2
Updated: 22nd December 2015
Author: CodersPress
Author URI: http://coderspress.com
License:
*/
add_action('admin_menu', 'value_added_tax');
function value_added_tax() {
	add_menu_page('Value Added Tax', 'Value Added Tax', 'administrator', __FILE__, 'value_added_tax_setting_page',plugins_url('/images/tax.gif', __FILE__));
	add_action( 'admin_init', 'register_value_added_tax_settings' );
}
function register_value_added_tax_settings() {
   	register_setting("value-added-tax-settings-group", "value_added_tax_amount");
   	register_setting("value-added-tax-settings-group", "value_added_tax_currency");
   	register_setting("value-added-tax-settings-group", "value_added_tax_text");
}

function value_added_tax_defaults()
{
    $option = array(
        "value_added_tax_amount" => "20",
        "value_added_tax_currency" => "$",
        "value_added_tax_text" => "Vat",
    );
  foreach ( $option as $key => $value )
    {
       if (get_option($key) == NULL) {
        update_option($key, $value);
       }
    }
    return;
}
register_activation_hook(__FILE__, "value_added_tax_defaults");

add_action( 'init', 'vat_plugin_updater' );
function vat_plugin_updater() {
	if ( is_admin() ) { 
	include_once( dirname( __FILE__ ) . '/updater.php' );
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'value-added-tax',
			'api_url' => 'https://api.github.com/repos/CodersPress/value-added-tax',
			'raw_url' => 'https://raw.github.com/CodersPress/value-added-tax/master',
			'github_url' => 'https://github.com/CodersPress/value-added-tax',
			'zip_url' => 'https://github.com/CodersPress/value-added-tax/zipball/master',
			'sslverify' => true,
			'access_token' => 'bfc28380ba54a471c2dc7bd2211abbb5cdf76cd5',
		);
		new WP_VAT_UPDATER( $config );
	}
}

function value_added_tax_setting_page() {
if ($_REQUEST['settings-updated']=='true') {
echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
}
?>
<div class="wrap">
    <h2>Value Added Tax - Settings</h2>
    <hr />
<form method="post" action="options.php">
    <?php settings_fields("value-added-tax-settings-group");?>
    <?php do_settings_sections("value-added-tax-settings-group");?>
    <table class="widefat" style="width:600px;">

<thead style="background:#2EA2CC;color:#fff;">
    <tr>
        <th style="color:#fff;">Vat Amount %</th>
        <th style="color:#fff;">Currency Symbol</th>
        <th style="color:#fff;">Tax,Vat - Other</th>
    </tr>
</thead>
<tr>
    <td>
        <input type="text" name="value_added_tax_amount" value="<?php echo get_option(" value_added_tax_amount ");?>">
        <br>Below 10% add Zero: Example 07.5</td>
    <td>
        <input type="text" name="value_added_tax_currency" value="<?php echo get_option(" value_added_tax_currency ");?>">
    </td>
    <td><input type="text" name="value_added_tax_text" value="<?php echo get_option(" value_added_tax_text");?>">
    </td>
</tr>
</table>
    <?php submit_button(); ?>
</form>
</div>
<?php
}
if(strstr($_SERVER['REQUEST_URI'], "invoiceid")) { 
session_start();
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
jQuery(document).ready(function () {
    var FinalTotal = jQuery('div.col-xs-2:nth-child(2) > strong:nth-child(1)').text().replace(/[^\d\.]/g, '');
    var vat = 1<?php echo get_option("value_added_tax_amount");?>;
    var preTax = (FinalTotal * 100) / vat;
    var preTotal = preTax.toFixed(2);
    var Tax = (FinalTotal - preTotal);
    jQuery('.table > tbody:nth-child(2) > tr:nth-child(1) > td:nth-child(3)').text('<?php echo get_option('value_added_tax_currency ');?>' + preTotal);
    jQuery('.table.table-bordered tr:last').after('<tr><td></td><td></td><td class="text-right"><?php echo get_option("value_added_tax_text");?>: <?php echo ltrim(get_option("value_added_tax_amount"), '0');?>%&nbsp;&nbsp;&nbsp;<?php echo get_option('value_added_tax_currency ');?>' + Tax.toFixed(2) + '</td></tr>');
});
</script>
<?php
}
?>