<table id="users" class="table table-bordered responsive">
    <colgroup>
        <col class="con1" />
    </colgroup>
    <thead>
    <tr>
        <th class="head1">Last</th>
        <th class="head1">Last</th>
        <th class="head1">Last</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script type="text/javascript">
    jQuery(document).ready(function(){
        // dynamic table
        jQuery('#users').dataTable({
            "sPaginationType": "full_numbers",
            "bProcessing": false,
            "bServerSide": true,
            "sAjaxSource": "{{ URL::to('auth/users/table') }}"
            //"fnDrawCallback": function(oSettings) {
            //    jQuery.uniform.update();
            //}
        });
    });
</script>