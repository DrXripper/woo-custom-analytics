
const addOrderTerminalFilters = ( filters ) => {

    return [
        {
            label: 'Terminal',
            staticParams: [],
            param: 'terminal',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderTerminals || [] ) ],
        },
        {
            label: 'Source',
            staticParams: [],
            param: 'source',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderSource || [] ) ],
        },
        {
            label: 'Store',
            staticParams: [],
            param: 'store',
            showFilters: () => true,
            defaultValue: '0',
            filters: [ ...( wcSettings.orderStore || [] ) ],
        },
        ...filters,
    ];
};
 
window.wp.hooks.addFilter( 'woocommerce_admin_orders_report_filters', 'dev-blog-example', addOrderTerminalFilters );
