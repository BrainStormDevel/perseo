<script src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script src="{$host}/modules/utenti/resources/gest_admins.js"></script>
<script>$( document ).ready(function() {
		   TableEditable.init();
		});
		urladd = '{$host}/{$ModuleUrlName}/admin/gestadmins/add_user/';
		urldel = '{$host}/{$ModuleUrlName}/admin/gestadmins/del_user/';
		lang = {
			edit : "{$l_edit}",
			save : "{$l_save}",
			cancel : "{$l_cancel}",
			delete : "{$l_delete}",
			ins_user : "{$l_ins_u}",
			ins_pass : "{$l_ins_p}",
			check_user : "{$l_check_u}",
			check_pass : "{$l_check_p}",
			check_email : "{$l_check_e}",
			table_lengthMenu : "{$l_length_menu}",
			table_zeroRecords : "{$l_info_empty}",
			table_info : "{$l_info}",
			table_infoEmpty : "{$l_info_empty}",
			table_infoFiltered : "{$l_info_filtered}",
			table_sSearch : "{$l_search}",
			table_sFirst : "{$l_first}",
			table_sPrevious : "{$l_previous}",
			table_sNext : "{$l_next}",
			table_sLast : "{$l_last}"
};</script>		