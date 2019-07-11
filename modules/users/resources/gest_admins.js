var TableEditable = function () {

    return {

        //main function to initiate the module
        init: function () {
			var oTable = $('#list_admins').DataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"ordering": true,
				"info": true,
				"autoWidth": true,
				"language": {
					"lengthMenu": lang.table_lengthMenu,
					"zeroRecords": lang.table_zeroRecords,
					"info": lang.table_info,
					"infoEmpty": lang.table_infoEmpty,
					"infoFiltered": lang.table_infoFiltered,
					"sSearch": lang.table_sSearch,
					"oPaginate": {
						"sFirst":      lang.table_sFirst,
						"sPrevious":   lang.table_sPrevious,
						"sNext":       lang.table_sNext,
						"sLast":       lang.table_sLast
					}
				}
			});

            var nEditing = null;
			
			function validateusername(username){
			var usernameReg = new RegExp(/^[a-z]([0-9a-z_])+$/i);
			var valid = usernameReg.test(username);
			if(!valid) {
				alert(lang.check_user);
				return false;
			} else {
				return true;
			}
			}
			
			function validatepassword(password){
			var passReg = new RegExp(/^([0-9a-zA-Z])+$/);
			var valid = passReg.test(password);
			if(!valid) {
				alert(lang.check_pass);
				return false;
			} else {
				return true;
			}
			}
			
			function validatemail(email){
			var emailReg = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			var valid = emailReg.test(email);
			if(!valid) {
				alert(lang.check_email);
				return false;
			} else {
				return true;
			}
			}
			
			function validateprivilegi(privilegi){
			if (privilegi<1 || privilegi>2) {
				alert("Controlla i privilegi assegnati");
				return false;
			} else {
				return true;
			}
			}			
            function restoreRow(oTable, nRow) {
                var aData = oTable.row(nRow).data();
				oTable.row(nRow).data(aData).draw;
            }
			
			function editRow(oTable, nRow, nNew) {
                var aData = oTable.row(nRow).data();
				var jqTds = $('>td', nRow);
				var test = aData[3];
				if(test.indexOf('@') > -1) { var email = aData[3]; }
				else { var email = ''; }
				if (!aData[0]) {
					jqTds[0].innerHTML = '<input type="hidden" value="0">';
				}
				else { jqTds[0].innerHTML = aData[0]; }
                jqTds[1].innerHTML = '<input type="text" class="m-wrap small" placeholder="' + lang.ins_user + '" value="' + aData[1] + '">';
                jqTds[2].innerHTML = '<input type="password" class="m-wrap small" placeholder="' + lang.ins_pass + '">';
                jqTds[3].innerHTML = '<input type="text" class="m-wrap small" placeholder="email@email.com" value="' + email + '">';
				jqTds[4].innerHTML = '<select class="m-wrap small" id="type"><option value="1">Admin</option><option value="2">Operatore</option></select>';
                jqTds[5].innerHTML = '<a class="edit" data-mode="save" href="javascript:void(0);"><i class="far fa-save" title="' + lang.save + '"></i></a>&nbsp;&nbsp;<a class="cancel" data-mode="' + nNew + '" href="javascript:void(0);"><i class="fas fa-ban" title="' + lang.cancel + '"></i></a>';
				if (aData[4] == "Operatore") {
					$("#type").val(2);
				}
            }
			
            function saveRow(oTable, nRow) {
				var aData = oTable.row(nRow).data();
                var jqInputs = $('input', nRow);
				var jqSelect = $('select', nRow);
				var id = jqInputs[0].value;
				var username = jqInputs[1].value;
				var password = jqInputs[2].value;
				var email = jqInputs[3].value;
				var objects = ['', '', '', '', '', ''];
				objects[0] = aData[0];
				objects[1] = username;
				objects[2] = '*****';
				objects[3] = email;
				objects[4] = jqSelect[0].options[jqSelect[0].selectedIndex].text;
				objects[5] = aData[5];
				var bValid = true;
				bValid = bValid && validateusername( username );
				bValid = bValid && validatepassword( password );
				bValid = bValid && validatemail( email );
				bValid = bValid && validateprivilegi( jqSelect[0].value );
				if ( bValid ) {
					var CSRFName = $('#CSRFName').val();
					var CSRFToken = $('#CSRFToken').val();
					$.ajax({
					url: urladd,
					type: 'POST',
					data: { [CSRFName] : CSRFToken, "id": id, "username": username, "password": password, "email": email, "privileges": jqSelect[0].value},
					success: function(response) {
						var result = JSON.parse(response);
						if (result.id > 0) {
							$('#CSRFName').val(result.AdminsCSRFname);
							$('#CSRFToken').val(result.AdminsCSRFToken);
							id = result.id;
						}
						var token_last = result.CSRFName + ':' + result.CSRFToken;
						objects[0] = '<input type="hidden" value="' + id + '">';
						oTable.row(nRow).data(objects).draw;
					}
					});
				}
            }
			
			function DeleteRow(oTable, nRow) {
				oTable.row(nRow).remove().draw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate("*****", nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate('<a class="edit" href="javascript:void(0);">' + lang.edit + '</a>', nRow, 4, false);
                oTable.fnDraw();
            }

            $('#add_new').on('click', function (e) {
                e.preventDefault();
				var aiNew = oTable.row.add( [ '', '', '', '', '', '<a class="edit" href="javascript:void(0);"><i class="fa fa-edit" title="' + lang.edit + '"></i></a>&nbsp;&nbsp;<a class="delete" data-mode="new" href="javascript:void(0);"><i class="fas fa-trash-alt" title="' + lang.delete + '"></i></a>' ] ).draw();
				var nRow = oTable.row(aiNew).node();
                editRow(oTable, nRow, 'new');
				nEditing = nRow;
            });

            $('#list_admins').on('click', 'a.delete', function (e) {
                e.preventDefault();
                var nRow = $(this).parents('tr')[0];
				var aData = oTable.row(nRow).data();
				var jqInputs = $('input', nRow);
				var curuser = aData[1];
				var id = jqInputs[0].value;
                if (confirm('Confermi di voler eliminare ' + curuser + '?') == false) {
                    return;
                }
				var CSRFName = $('#CSRFName').val();
				var CSRFToken = $('#CSRFToken').val();				
				$.ajax({
					url: urldel,
					type: 'POST',
					data: { [CSRFName] : CSRFToken, "id": id},
					success: function(response) {
						var result = JSON.parse(response);
						alert(result.msg);
						$('#CSRFName').val(result.AdminsCSRFname);
						$('#CSRFToken').val(result.AdminsCSRFToken);
						if (result.record != 0) { DeleteRow(oTable, nRow); }
					}
				});
            });

            $('#list_admins').on('click', 'a.cancel', function (e) {
                e.preventDefault();
                if ($(this).attr("data-mode") == "old") {
                    restoreRow(oTable, nEditing);
                    nEditing = null;
                } else {
					var nRow = $(this).parents('tr')[0];
                    DeleteRow(oTable, nRow);
					nEditing = null;
                }
            });
			
            $('#list_admins').on('click', 'a.mostra', function (e) {
                e.preventDefault();
				var nRow = $(this).parents('tr')[0];
                var aData = oTable.row(nRow).data();
				var jqInputs = $('input', nRow);
				var id = jqInputs[0].value;
				var token = jqInputs[1].value.split(":");
				$.ajax({
					url: 'index.php?nome=gestusers',
					type: 'post',
					data: { "act": "show_admin_email", "int_id": id, "CSRFName": token[0], "CSRFToken": token[1]},
					success: function(response) {
						result = JSON.parse(response);
						var objects = ['', '', '', '', '', ''];
						var token_last = result.CSRFName + ':' + result.CSRFToken;
						objects[0] = '<input type="hidden" value="' + id + '"><input type="hidden" value="' + token_last + '">';
						objects[1] = aData[1];
						objects[2] = aData[2];
						objects[3] = result.email;
						objects[4] = aData[4];
						objects[5] = aData[5];
						oTable.row(nRow).data(objects).draw;
						nEditing = null;
					}
				});
            });

            $('#list_admins').on('click', 'a.edit', function (e) {
                e.preventDefault();
				    /* Get the row as a parent of the link that was clicked on */
                var nRow = $(this).parents('tr')[0];
                if (nEditing !== null && nEditing != nRow) {
                    /* Currently editing - but not this row - restore the old before continuing to edit mode */
                    restoreRow(oTable, nEditing);
                    editRow(oTable, nRow, 'old');
                    nEditing = nRow;
                } else if (nEditing == nRow && $(this).attr("data-mode") == "save") {
                    /* Editing this row and want to save it */
                    saveRow(oTable, nEditing);
                    nEditing = null;
					e.originalEvent;
                    /*alert("Updated! Do not forget to do some ajax to sync with backend :)");*/
                } else {
                    /* No edit in progress - let's start one */
                    editRow(oTable, nRow, 'old');
					nEditing = nRow;
                }

            });
        }

    };

}();