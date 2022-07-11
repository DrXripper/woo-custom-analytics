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
    $pagepath = $_GET['path'];

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


function add_order_terminal_settings() {
    global $wpdb;  
        

    $order_terminals = $wpdb->get_results("SELECT pm.meta_value FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id  WHERE p.post_type = 'shop_order' AND pm.meta_key='_order_terminal' GROUP BY pm.meta_value "); 

    $order_stores = $wpdb->get_results("SELECT pm.meta_value FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id  WHERE p.post_type = 'shop_order' AND pm.meta_key='_order_store' GROUP BY pm.meta_value "); 

    $order_sources = $wpdb->get_results("SELECT pm.meta_value FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id  WHERE p.post_type = 'shop_order' AND pm.meta_key='_order_source' GROUP BY pm.meta_value "); 

    $order_terminals_options[] = array( 'label' => 'All Terminals', 'value' => '0');
    if($order_terminals){  
        foreach ( $order_terminals as $key=> $row ) { 
            $order_terminals_options[] =  array(
                'label' => $row->meta_value,
                'value' => $row->meta_value,
            );
        }
    }

    $order_stores_options[] = array( 'label' => 'All Sources', 'value' => '0');
    if($order_stores){  
        foreach ( $order_stores as $key=> $row ) { 
            $order_stores_options[] =  array(
                'label' => $row->meta_value,
                'value' => $row->meta_value,
            );
        }
    }

    $order_sources_options[] = array( 'label' => 'All Stores', 'value' => '0');
    if($order_sources){  
        foreach ( $order_sources as $key=> $row ) { 
            $order_sources_options[] =  array(
                'label' => $row->meta_value,
                'value' => $row->meta_value,
            );
        }
    }


    $data_registry = Automattic\WooCommerce\Blocks\Package::container()->get(
        Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class
    );
 
    $data_registry->add( 'orderTerminals', $order_terminals_options );
    $data_registry->add( 'orderSource', $order_sources_options);
    $data_registry->add( 'orderStore', $order_stores_options  );
}
 
add_action( 'admin_init', 'add_order_terminal_settings' );


function apply_currency_arg( $args ) {
    if ( isset( $_GET['terminal'] ) ) {
        $terminal = sanitize_text_field( wp_unslash( $_GET['terminal'] ) );
        $args['terminal'] = $terminal;
    }
    if ( isset( $_GET['source'] ) ) {
        $source = sanitize_text_field( wp_unslash( $_GET['source'] ) );
        $args['source'] = $source;
    }
    if ( isset( $_GET['store'] ) ) {
        $store = sanitize_text_field( wp_unslash( $_GET['store'] ) );
        $args['store'] = $store;
    }
    return $args;
}
 
add_filter( 'woocommerce_analytics_orders_query_args', 'apply_currency_arg' );
add_filter( 'woocommerce_analytics_orders_stats_query_args', 'apply_currency_arg' );


function add_join_subquery( $clauses ) {
    global $wpdb; 
    if ( isset( $_GET['terminal'] ) ) {
        $clauses[] = "JOIN {$wpdb->postmeta} pm ON {$wpdb->prefix}wc_order_stats.order_id = pm.post_id"; 
    }
    if ( isset( $_GET['source'] ) ) {
        $clauses[] = "JOIN {$wpdb->postmeta} pm2 ON {$wpdb->prefix}wc_order_stats.order_id = pm2.post_id";
    } 
    if ( isset( $_GET['store'] ) ) {
        $clauses[] = "JOIN {$wpdb->postmeta} pm3 ON {$wpdb->prefix}wc_order_stats.order_id = pm3.post_id"; 
    }
    return $clauses;
}
 
add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', 'add_join_subquery' );


function add_where_subquery( $clauses ) {
    if ( isset( $_GET['terminal'] ) ) {
        $terminal = sanitize_text_field( wp_unslash( $_GET['terminal'] ) );
        $clauses[] = " AND pm.meta_key = '_order_terminal' AND pm.meta_value = '{$terminal}'";
    }
    if ( isset( $_GET['source'] ) ) {
        $source = sanitize_text_field( wp_unslash( $_GET['source'] ) );
        $clauses[] = " AND pm2.meta_key = '_order_source' AND pm2.meta_value = '{$source}'";
    }
    if ( isset( $_GET['store'] ) ) {
        $store = sanitize_text_field( wp_unslash( $_GET['store'] ) );
        $clauses[] = " AND pm3.meta_key = '_order_store' AND pm3.meta_value = '{$store}'";
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