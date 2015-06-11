<?php

/*

Form analytics DEMO

*/

if ( ! defined( 'ABSPATH' ) ) exit;


function easycform_form_analytics() {
    $easycform_featured_init = add_submenu_page('edit.php?post_type=easycontactform', 'Form Analytics', __('Analytics', 'easycform'), 'edit_posts', 'easycform_form_analytics', 'easycform_analytics');
}

function easycform_put_load_google_cart() {
	if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'easycform_form_analytics' ){
	wp_register_style('ecf_googleFonts', 'http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700');
    wp_enqueue_style( 'ecf_googleFonts');
	wp_enqueue_script( 'ecf_jsapi', 'https://www.google.com/jsapi');	
		}
}
add_action( 'admin_enqueue_scripts', 'easycform_put_load_google_cart' );

function easycform_analytics() {

?>

<!-- Chart Table Start -->
<style>
.cfp-main-form {
    width: 100%;
    background-color: #FFF;
    max-width: 883px;
    float: left;
    box-shadow: 0px 1px 2px -1px #C7C7C7;
    margin-left: 30px;
    margin-top: 35px;
    font-family: 'Roboto',sans-serif;
}

.cfp-main-form .charts {
    margin-bottom: 0px !important;
}

.cfp-main-form .ecf-main-chat {
    width: 100% !important;
    float: left;
    margin-right: 83px;
    background: none repeat scroll 0% 0% #FFF;
    box-shadow: 0px 1px 2px -1px rgb(199, 199, 199) !important;
}

.ecfcler {
    clear: both;
}

.ecf-main-chat h1 {
    margin-bottom: 0px !important;
    display: block;
    padding-bottom: 10px;
    border-bottom: 1px solid #E1E1E1;
    min-height: 30px;
    font-family: "Roboto",sans-serif;
    font-weight: normal;
    margin-top: 10px !important;
    font-size: 24px;
    color: #78B0BE;
    padding-left: 20px;
    line-height: 30px;
}

</style>

<script type="text/javascript">
      google.load('visualization', '1.0', {'packages':['corechart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Status');
        data.addColumn('number', 'Total');
        data.addRows([
			['Contact Us Form', 1913],
			['Registration Form', 1003],
			['Feedback Form', 1100],
			['Billing Information Form', 819],
			['Support Request Form', 531],
        ]);

        var options = {
		is3D: true,
		
		};
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

	 
<div class="cfp-main-form cfp-main-form2">
  <div class="charts">
    <div class="ecf-main-chat">
    <h1 class="chat-hedding">Form Analytics
     <span class="icon"></span>
    </h1>
    <p style="padding-left: 20px;border-bottom: 1px solid #E1E1E1;padding-bottom: 10px;">This feature only available in <strong>Pro Version</strong>. You can see version comparison <a style="text-decoration:none !important;" href="<?php echo admin_url( 'edit.php?post_type=easycontactform&page=easycform_comparison' ); ?>">here</a>.</p>
    <div style="width: 100%; height: 400px;" class="chartdiv" id="chart_div"></div>
    </div>
 
    <div class="ecfcler"></div>
    </div>
    
<?php

}