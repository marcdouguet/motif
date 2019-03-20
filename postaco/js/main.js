(function($) {
    $(document).ready(function() {        
      
        var tfConfigSummary = {
            base_path: 'js/TableFilter/dist/tablefilter/',
            auto_filter: true,
            loader: true,
            //rows_counter: true,
            extensions: [{
                name: 'sort'
                }]
            };
     
            var tf = new TableFilter("results", tfConfigSummary);
            tf.init();

             
    });
})(jQuery);
