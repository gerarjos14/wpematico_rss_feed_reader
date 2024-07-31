jQuery(document).ready(function ($) {
    // Function to toggle visibility of dropdowns based on selected radio button
    function toggleDropdowns() {
        if ($('#campaign_type').val() == 'rss_reader') {
            var selectedValue = $('input[name="campaign_rss_feed_reader"]:checked').val();

            // Show the corresponding dropdown based on the selected value
            if (selectedValue === 'the_content') {
                $('#custom-dropdown-posts').show();
                $('#custom-dropdown-pages').hide();
                $('#rss_page_template').hide();
                toggleCustomTypes();
                $('input[name="campaign_customposttype"]').not(':radio[name="campaign_customposttype"][value="page"]').prop('disabled', false);
            } else if (selectedValue === 'page_template') {
                $('#custom-dropdown-pages').show();
                if($('#campaign_page_select').val()){
                    $('#rss_page_template').show();
                }
                $('#custom-dropdown-posts').hide();
                
                $('input[name="campaign_customposttype"]').not(':radio[name="campaign_customposttype"][value="page"]').prop('disabled', true);

                $(':radio[name="campaign_customposttype"][value="page"]').prop('checked', true);
            } else {
                $('#custom-dropdown-posts').hide();
                $('#custom-dropdown-pages').hide();
                $('#rss_page_template').hide();

                $('input[name="campaign_customposttype"]').not(':radio[name="campaign_customposttype"][value="page"]').prop('disabled', false);
            }
        }
    }

    // Initial call to toggleDropdowns to set initial state
    toggleDropdowns();
    toggleCustomTypes();

    function toggleCustomTypes() {
        if ($('#campaign_type').val() == 'rss_reader') {
            var postTypes = $('input[name="campaign_customposttype"]:checked').val();
            var selectedValue = $('input[name="campaign_rss_feed_reader"]:checked').val();

            if (selectedValue !== 'shortcode') {
                if (selectedValue) {
                    if (postTypes == 'post') {
                        $('#custom-dropdown-posts').show();
                        $('#custom-dropdown-pages').hide();
                    } else if (postTypes == 'page') {
                        $('#custom-dropdown-pages').show();
                        $('#custom-dropdown-posts').hide();
                    } else {
                        $('#custom-dropdown-posts').hide();
                        $('#custom-dropdown-pages').hide();
                    }
                }
            }
        }
    }

    $('input[name="campaign_customposttype"]').on('change', function () {
        if ($('#campaign_type').val() == 'rss_reader') {
            toggleCustomTypes();
        }
    });
    // Call toggleDropdowns whenever the radio buttons change
    $('input[name="campaign_rss_feed_reader"]').on('change', function () {
        if ($('#campaign_type').val() == 'rss_reader') {
            toggleDropdowns();
        }
    });

    $('#campaign_type').on('change', function () {
        if ($('#campaign_type').val() != 'rss_reader') {
            $('#custom-dropdown-posts').hide();
            $('#custom-dropdown-pages').hide();
        }else{
            toggleCustomTypes();
        }
    });
    $('#campaign_page_select').on('change', function () {
        if($('#campaign_page_select').val()){
            toggleDropdowns();
        }else{
            $('#rss_page_template').hide();
        }
    });
    $('#campaign_max_to_show , #campaign_max').on('blur', function (){
        if ($('#campaign_type').val() == 'rss_reader') {
            if ($('#campaign_max_to_show').val() !== $('#campaign_max').val()) {
                $('#fieldserror').remove();
                $("#poststuff").prepend('<div id="fieldserror" class="error fade">ERROR: ' + backend_object_rss.error_message + '</div>');
            } else {
                $('#fieldserror').remove();
            }
        }
    });
});