<?php
/**
 * Plugin Name: Konbini Analytics Custom Filter
 * Description: Adds custom filters inside woocommerce analytics.
 * Version: 1.0
 * Author: Prathamesh Kothavale
 * Author URI: https://konbitech.com/
 * Text Domain: woofilters
 *
 */
 


/*WooCommerce Analytics Filters*/
 
add_action('admin_footer', 'addOrderAnalyticsjs');
function addOrderAnalyticsjs(){
    if(isset($_GET['path']) && $_GET['path']){ 
        $pagepath = @$_GET['path'];  
        if($pagepath == '/analytics/orders'){
            ?>
            <style type="text/css"> 
                .woocommerce-filters-filter:nth-child(2), .woocommerce-filters-filter:nth-child(3), .woocommerce-filters-filter:nth-child(4), .woocommerce-filters-filter:nth-child(5){
                    width: 20%;
                }
            </style> 
            <?php 
        }
    }
}


function add_order_terminal_settings() {
    global $wpdb;   

        $locationOptions = (array)get_terms( 'filter_options', array(  'hide_empty' => 0,'description__like' => 'locations')); 
        $machineOptions = (array)get_terms( 'filter_options', array(  'hide_empty' => 0,'description__like' => 'machines' )); 
       /* $order_terminals = (array)get_terms( 'filter_options', array(  'hide_empty' => 0,'description__like' => 'terminals'));
        $order_sources = (array)get_terms( 'filter_options', array(  'hide_empty' => 0, 'description__like' => 'sources')); 
        $order_stores = (array)get_terms( 'filter_options', array(  'hide_empty' => 0,'description__like' => 'stores'));*/ 

    $order_terminals = $wpdb->get_results("SELECT pm.meta_value FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id  WHERE p.post_type = 'shop_order' AND pm.meta_key='_order_terminal' GROUP BY pm.meta_value "); 

    $order_stores = $wpdb->get_results("SELECT pm.meta_value FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id  WHERE p.post_type = 'shop_order' AND pm.meta_key='_order_store' GROUP BY pm.meta_value "); 

    $order_sources = $wpdb->get_results("SELECT pm.meta_value FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id  WHERE p.post_type = 'shop_order' AND pm.meta_key='_order_source' GROUP BY pm.meta_value "); 

    if($locationOptions){  
        $order_location_options[] = array( 'label' => 'All Locations', 'value' => '0');
        foreach ( $locationOptions as $key=> $row ) { 
            $order_location_options[] =  array(
                'label' => $row->name,
                'value' => $row->name,
            );
        }
    }

    if($machineOptions){  
        $order_machines_options[] = array( 'label' => 'All Machines', 'value' => '0');
        foreach ( $machineOptions as $key=> $row ) { 
            $order_machines_options[] =  array(
                'label' => $row->name,
                'value' => $row->name,
            ); 
        }
    }

    if($order_terminals){   
        $order_terminals_options[] = array( 'label' => 'All Terminals', 'value' => '0');
        foreach ( $order_terminals as $key=> $row ) { 
            $order_terminals_options[] =  array(
                'label' => $row->meta_value,
                'value' => $row->meta_value,
            );
        } 
    }

    if($order_stores){  
        $order_stores_options[] = array( 'label' => 'All Sources', 'value' => '0');
        foreach ( $order_stores as $key=> $row ) { 
            $order_stores_options[] =  array(
                'label' => $row->meta_value,
                'value' => $row->meta_value,
            );
        }
    }

    if($order_sources){  
        $order_sources_options[] = array( 'label' => 'All Stores', 'value' => '0');
        foreach ( $order_sources as $key=> $row ) { 
            $order_sources_options[] =  array(
                'label' => $row->meta_value,
                'value' => $row->meta_value,
            );
        }
    } 

    
    $gateways = WC()->payment_gateways->get_available_payment_gateways();  
    if( $gateways ) {
        $paymentMethods[] = array( 'label' => 'All Payment Methods', 'value' => '0');  
        foreach( $gateways as $gateway ) { 
            if( $gateway->enabled == 'yes' ) { 
                $paymentMethods[] = array( 'label' => $gateway->get_title(), 'value' => esc_attr( $gateway->id));  
            }
        }
    }

      

     $order_statuses = get_posts( array('post_type'=>'wc_order_status', 'post_status'=>'publish', 'numberposts'=>-1) ); 
      $orderStatus[] = array( 'label' => 'All Order Status', 'value' => '0'); 
      foreach ( $order_statuses as $status ) { 
        $orderStatus[] = array( 'label' => $status->post_title, 'value' =>'wc-'.$status->post_name); 
      }
         



    $data_registry = Automattic\WooCommerce\Blocks\Package::container()->get(
        Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class
    );

    if(isset($order_location_options) && $order_location_options) 
    $data_registry->add( 'orderLocations', $order_location_options );
    
    if(isset($order_machines_options) && $order_machines_options)
    $data_registry->add( 'orderMachines', $order_machines_options ); 
    
    if(isset($order_terminals_options) && $order_terminals_options)
    $data_registry->add( 'orderTerminals', $order_terminals_options );

    if(isset($order_sources_options) && $order_sources_options)
    $data_registry->add( 'orderSource', $order_sources_options);

    if(isset($order_stores_options) && $order_stores_options)
    $data_registry->add( 'orderStore', $order_stores_options  ); 

    if(isset($paymentMethods) && $paymentMethods)
    $data_registry->add( 'paymentMethods', $paymentMethods  );

    if(isset($orderStatus) && $orderStatus)
    $data_registry->add( 'orderStatus', $orderStatus);    
}
 
add_action( 'admin_init', 'add_order_terminal_settings' );


function apply_currency_arg( $args ) {
    if ( isset( $_GET['terminal'] ) && !empty($_GET['terminal'])) {
        $terminal = sanitize_text_field( wp_unslash( $_GET['terminal'] ) );
        $args['terminal'] = $terminal;
    }
    if ( isset( $_GET['source'] ) && !empty($_GET['source'])) {
        $source = sanitize_text_field( wp_unslash( $_GET['source'] ) );
        $args['source'] = $source;
    }
    if ( isset( $_GET['store'] ) && !empty($_GET['store'])) {
        $store = sanitize_text_field( wp_unslash( $_GET['store'] ) );
        $args['store'] = $store;
    }

    if ( isset( $_GET['paymentMethods'] ) && !empty($_GET['paymentMethods'])) {
        $paymentMethods = sanitize_text_field( wp_unslash( $_GET['paymentMethods'] ) );
        $args['paymentMethods'] = $paymentMethods;
    }

    if ( isset( $_GET['orderStatus'] ) && !empty($_GET['orderStatus'])) {
        $orderStatus = sanitize_text_field( wp_unslash( $_GET['orderStatus'] ) );
        $args['store'] = $orderStatus;
    }
    return $args;
}
 
add_filter( 'woocommerce_analytics_orders_query_args', 'apply_currency_arg' );
add_filter( 'woocommerce_analytics_orders_stats_query_args', 'apply_currency_arg' );


function add_join_subquery( $clauses ) {
    global $wpdb; 
    if ( isset( $_GET['terminal'] ) && !empty($_GET['terminal'])) {
        $clauses[] = " JOIN {$wpdb->postmeta} pm ON {$wpdb->prefix}wc_order_stats.order_id = pm.post_id "; 
    }
    if ( isset( $_GET['source'] ) && !empty($_GET['source'])) {
        $clauses[] = " JOIN {$wpdb->postmeta} pm2 ON {$wpdb->prefix}wc_order_stats.order_id = pm2.post_id ";
    } 
    if ( isset( $_GET['store'] ) && !empty($_GET['store'])) {
        $clauses[] = " JOIN {$wpdb->postmeta} pm3 ON {$wpdb->prefix}wc_order_stats.order_id = pm3.post_id "; 
    }

    if ( isset( $_GET['paymentMethods'] ) && !empty($_GET['paymentMethods'])) { 
        $clauses[] = " JOIN {$wpdb->postmeta} pm4 ON {$wpdb->prefix}wc_order_stats.order_id = pm4.post_id "; 
    }  
    return $clauses;
}
 
add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', 'add_join_subquery' );


function add_where_subquery( $clauses ) {   
     global $wpdb;  
    if ( isset( $_GET['orderStatus'] ) && !empty($_GET['orderStatus'])) {  
        $orderStatus = sanitize_text_field( wp_unslash( $_GET['orderStatus'] ) );
        $clauses[] = " AND {$wpdb->prefix}wc_order_stats.status = '{$orderStatus}'";         
    }     

    if ( isset( $_GET['terminal'] ) && !empty($_GET['terminal'])) {
        $terminal = sanitize_text_field( wp_unslash( $_GET['terminal'] ) );
        $clauses[] = " AND pm.meta_key = '_order_terminal' AND pm.meta_value = '{$terminal}' ";
    }

    if ( isset( $_GET['source'] ) && !empty($_GET['source'])) {
        $source = sanitize_text_field( wp_unslash( $_GET['source'] ) );
        $clauses[] = " AND pm2.meta_key = '_order_source' AND pm2.meta_value = '{$source}' ";
    }  

    if ( isset( $_GET['store'] ) && !empty($_GET['store'])) {
        $store = sanitize_text_field( wp_unslash( $_GET['store'] ) );
        $clauses[] = " AND pm3.meta_key = '_order_store' AND pm3.meta_value = '{$store}' ";
    }

     if ( isset( $_GET['paymentMethods'] ) && !empty($_GET['paymentMethods'])) {
        $paymentMethods = sanitize_text_field( wp_unslash( $_GET['paymentMethods'] ) );
        $clauses[] = " AND pm4.meta_key = '_payment_method' AND pm4.meta_value = '{$paymentMethods}' ";
    }  
    return $clauses; 
}
 
add_filter( 'woocommerce_analytics_clauses_where_orders_subquery', 'add_where_subquery' );
add_filter( 'woocommerce_analytics_clauses_where_orders_stats_total', 'add_where_subquery' );
add_filter( 'woocommerce_analytics_clauses_where_orders_stats_interval', 'add_where_subquery' );

 
function enqueuing_admin_scripts(){ 
     wp_enqueue_script('admin_analicts', plugin_dir_url( __FILE__ ).'/js/admin-analicts.js', array(), '1.0.0', true); 
} 
add_action( 'admin_enqueue_scripts', 'enqueuing_admin_scripts' );  

 
