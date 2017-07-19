<?php require_once('conn.php'); 
if(isset($_REQUEST['task_id']))
{
	$task_id = $_REQUEST['task_id'];
	$sel_stmt = $conn->query("select task_id,task_name,priority,assign_to,start_date,end_date,task_desc,assign_to from tasks where status='Active' and task_id='".$task_id."'");
	$sel_row = $sel_stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Task Management</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/datepicker.css" rel="stylesheet">
	<style type="text/css">
	.form-group.required .control-label:after {
		content:"*";	
		color:red;
	}
	</style>
</head>

<body>

    <div style="overflow-x:hidden">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Task Management</a>
            </div>
        </nav>

        <div id="page-wrapper">
<div class="container">
<div class="row">
<h3>Task Management Form</h3>
<hr>
  <form role="form" class="form-horizontal" method="post" name="task_form" id="task_form" autocomplete="off">
  <input type="hidden" name="action" value="save">
  <input type="hidden" class="form-control" id="task_id" name="task_id" value="<?php echo isset($sel_row['task_id'])?$sel_row['task_id']:''; ?>" >
    <div class="form-group required">
      <label class="col-sm-2 control-label">Task Name</label>
      <div class="col-sm-5">
		<input type="text" class="form-control" id="task_name" name="task_name" value="<?php echo isset($sel_row['task_name'])?$sel_row['task_name']:''; ?>" placeholder="Task Name">
	  </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label">Assign To</label>
      <div class="col-sm-5">
		<select class="form-control" id="assign_to" name="assign_to">
			<option value="">--Select--</option>
			<?php
			$stmt = $conn->query("select user_id,user_name from users where status='Active'");
			while ($row = $stmt->fetch())
			{
				echo '<option '.((isset($sel_row['assign_to'])&&$sel_row['assign_to']==$row['user_id'])?'selected':'').' value="'.$row['user_id'].'">'.$row['user_name'].'</option>';
			} ?>
		</select>
	  </div>
    </div>
	<div class="form-group required">
      <label class="col-sm-2 control-label">Priority</label>
      <div class="col-sm-5">
		<select class="form-control" name="priority" id="priority" >
			<option value="">--Select--</option>
			<option <?php echo ((isset($sel_row['priority'])&&$sel_row['priority']=='high')?'selected':'') ?>  value="high">High</option>
			<option <?php echo ((isset($sel_row['priority'])&&$sel_row['priority']=='medium')?'selected':'') ?>  value="medium">Medium</option>
			<option <?php echo ((isset($sel_row['priority'])&&$sel_row['priority']=='low')?'selected':'') ?>  value="low">Low</option>
		</select>
	  </div>
    </div>
	 <div class="form-group required">
      <label class="col-sm-2 control-label">Start Date</label>
      <div class="col-sm-5">
		<input type="text" class="form-control" id="start_date" name="start_date" value="<?php echo isset($sel_row['start_date'])?date('d-m-Y',strtotime($sel_row['start_date'])):''; ?>" placeholder="Start Date">
	  </div>
    </div>
	<div class="form-group required">
      <label class="col-sm-2 control-label">End Date</label>
      <div class="col-sm-5">
		<input type="text" class="form-control" id="end_date" value="<?php echo isset($sel_row['end_date'])?date('d-m-Y',strtotime($sel_row['end_date'])):''; ?>" name="end_date" placeholder="End Date">
	  </div>
    </div>
	<div class="form-group">
      <label class="col-sm-2 control-label">Description</label>
      <div class="col-sm-5">
		<textarea class="form-control" id="task_desc" name="task_desc" placeholder="Description"><?php echo isset($sel_row['task_desc'])?$sel_row['task_desc']:''; ?></textarea>
	  </div>
    </div>
    <div class="form-group">
      <div class="col-sm-7">
        <button type="submit" id="btnSubmit" class="btn btn-info pull-right"><?php echo isset($sel_row['task_id'])?'Update':'Submit'; ?></button>
        <a style="margin-right:5px" href="javascript:void(0)" onclick="reset()" class="btn btn-info pull-right">Reset</a>
      </div>
    </div>
  </form>
  </div>
  <div class="row">
                    <div class="col-lg-10">
                        <h2>Tasks List</h2>
                        <div class="table-responsive" style="max-height:250px;overflow-y:auto;">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>SlNo.</th>
                                        <th>Task Name</th>
                                        <th>Assign To</th>
                                        <th>Priority</th>
                                        <th>StartDate</th>
                                        <th>EndDate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tblData">
								
                                </tbody>
                            </table>
                        </div>
                    </div>
					</div>
</div>
    <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			load_data();
			$('#start_date').datepicker({
				format:'dd-mm-yyyy',
				startDate: "today",
				autoclose: true
			}).on('changeDate', function (selected) {
				var minDate = new Date(selected.date.valueOf());
				$('#end_date').datepicker('setStartDate', minDate);
			});
			$('#end_date').datepicker({
				format:'dd-mm-yyyy',
				startDate: "today",
				autoclose: true
			}) .on('changeDate', function (selected) {
					var maxDate = new Date(selected.date.valueOf());
					$('#start_date').datepicker('setEndDate', maxDate);
			});
			$("form").submit(function(e){
                if($('#task_name').val()!=''&&$('#assign_to').val()!=''&&$('#priority').val()!=''&&$('#start_date').val()!=''&&$('#end_date').val()!='')
			{
				$.ajax({
					url:'ajax_submit.php',
					type:'POST',
					data:$('#task_form').serialize(),
					success:function(result)
					{
						alert(result);
						load_data();
						reset();
					}
				});
			}
			else
			{
				alert('Please fill the form fields');
			}
                e.preventDefault(e);
            });
		});
		function del_task(task_id)
		{
			if(confirm('Are u confirm to delete?'))
			{
				$.ajax({
					url:'ajax_submit.php',
					data:{action:'delete',task_id:task_id},
					type:'POST',
					success:function(result)
					{
						alert(result);
						load_data();
						reset();
					}
				});
			}				
		}
		function load_data()
		{
			$.ajax({
					url:'ajax_submit.php',
					data:{action:'data'},
					type:'POST',
					success:function(result)
					{
						$('#tblData').html(result);
					}
				});
		}
		function reset()
		{
			$('#task_form').find("input[type=text],textarea,select").val("");
			$('#task_id').val('');
			$('#btnSubmit').html('Submit');
		}
	</script>
	
</body>
</html>
