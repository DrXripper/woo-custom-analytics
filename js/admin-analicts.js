
const addOrderTerminalFilters = ( filters ) => {

    var changefilters = [];

    if(wcSettings.orderTerminals){
         changefilters.push({
            label: 'Terminal',
            staticParams: [],
            param: 'terminal',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderTerminals || [] ) ],
        });
    }

    if(wcSettings.orderSource){
         changefilters.push({
            label: 'Source',
            staticParams: [],
            param: 'source',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderSource || [] ) ],
        });
    }

    if(wcSettings.orderStore){
         changefilters.push({
            label: 'Store',
            staticParams: [],
            param: 'store',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderStore || [] ) ],
        });
    }

    if(wcSettings.paymentMethods){
         changefilters.push({
            label: 'Payment Methods',
            staticParams: [],
            param: 'paymentMethods',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.paymentMethods || [] ) ],
        });
    }

    if(wcSettings.orderStatus){
         changefilters.push({
            label: 'Order Status',
            staticParams: [],
            param: 'orderStatus',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderStatus || [] ) ],
        });
    }

    changefilters.push( ...filters );     

    return changefilters;
};
 
window.wp.hooks.addFilter( 'woocommerce_admin_orders_report_filters', 'dev-blog-example', addOrderTerminalFilters );
