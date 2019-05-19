        <section class="content-header">
          <h1>
            {$l_users_panel}
            <small>{$l_users_panel_info}</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="{$adm_host}/"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{$host}/{$ModuleUrlName}/admin/">{$l_gest_users}</a></li>
            <li class="active">{$l_gest_admin}</li>
          </ol>
        </section>
		<!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">{$l_gest_admin}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
				 <a id="add_new" href="javascript:void(0);" class="btn btn-flat btn-primary">{$l_add_admin}</a><br><br>
				 <input name="CSRFName" id="CSRFName" type="hidden" value="{$AdminsCSRFname}">
				 <input name="CSRFToken" id="CSRFToken" type="hidden" value="{$AdminsCSRFToken}">
                  <table id="list_admins" class="table table-bordered table-striped">
                    <thead>
                      <tr>
						<th></th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email</th>
                        <th>{$l_role}</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
					{if $admtot>0}
					  {for $i=0 to $admtot-1}
					  <tr>
						<td><input type="hidden" value="{$admins[{$i}].id}"></td>
						<td>{$admins[{$i}].user}</td>
                        <td>*****</td>
                        <td>{$admins[{$i}].email}</td>
                        <td>{if $admins[$i].privilegi eq '1'}Admin{elseif $admins[$i].privilegi eq '2'}Operatore{else}{$admins[{$i}].privilegi}{/if}</td>
                        <td>{if $privileges <= $admins[$i].privilegi}<a class="edit" href="javascript:void(0);"><i class="fas fa-edit" title="{$l_edit}"></i></a>&nbsp;&nbsp;<a class="delete" href="javascript:void(0);"><i class="fas fa-trash-alt" title="{$l_delete}"></i></a>{/if}</td>
					  </tr>
					  {/for}
					{/if}  
                    </tbody>
                    <tfoot>
                      <tr>
						<th></th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email</th>
                        <th>{$l_role}</th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->