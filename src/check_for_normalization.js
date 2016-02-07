$(window).load(function(){
    jQuery(document).ready(function ($) {
        jQuery.get('data/initializer.csv', function(data) {
            var normalized = data.substr(0, data.indexOf("\n"));

            if (normalized.trim() != 'Normalized'){
                if (confirm("The 'initializer.csv' file is not normalized.\nDo you want to normalize it now?") == true) {
                    console.log("Starting normalization of the 'initializer.csv' file");

                    // normalizez csv file and creates simple links instead of directional ones
                    $.ajax({
                        type: "POST",
                        url: "src/php_files/normalize_csv.php",
                        async: false,
                        success: function(e) {console.log('Normalized CSV file'); if (e) console.log(e);},
                        error: function(e) {console.log('Error normalizing CSV file'); if (e) console.log(e);}
                    });
                } else {
                    console.log("Normalization of the 'initializer.csv' file was canceled. Simulator might not run correctly");
                }
            }
        });

    
    });
});