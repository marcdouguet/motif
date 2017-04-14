(function($) {
    $(document).ready(function() {        
      
        var tfConfigSummary = {
            base_path: 'js/TableFilter/dist/tablefilter/',
            loader: true,
            rows_counter: true,
            extensions: [{
                name: 'sort'
                }]
            };
     
            var tf = new TableFilter("results", tfConfigSummary);
            tf.init();

             
    });
})(jQuery);
