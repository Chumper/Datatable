<script type="text/javascript">
    jQuery(document).ready(function(){
        // dynamic table
        oTable = jQuery('#{{ $id }}').dataTable({{ $options_string }});
    // custom values are available via $values array
    });
</script>
