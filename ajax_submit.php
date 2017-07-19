<?php
require_once('conn.php'); //........... Database Connection File.................
$action = isset($_POST['action'])?$_POST['action']:'';
//.......... Insert and Update ........................
if($action=='save')
{
	$task_id = isset($_POST['task_id'])?$_POST['task_id']:'';
	$task_name = isset($_POST['task_name'])?$_POST['task_name']:'';
	$priority = isset($_POST['priority'])?$_POST['priority']:'';
	$assign_to = isset($_POST['assign_to'])?$_POST['assign_to']:'';
	$start_date = isset($_POST['start_date'])?$_POST['start_date']:'';
	if($start_date!='')
		$start_date = date('Y-m-d',strtotime($start_date));
	$end_date = isset($_POST['end_date'])?$_POST['end_date']:'';
	if($end_date!='')
		$end_date = date('Y-m-d',strtotime($end_date));
	$task_desc = isset($_POST['task_desc'])?$_POST['task_desc']:'';
	$date = date('Y-m-d H:i:s');
	$status = 'Active';
	if($task_name!=''&&$priority!=''&&$assign_to!=''&&$start_date!=''&&$end_date!='')
	{
		//........... Insert .................
		try {
			if($task_id=='')
			{
				$sql = "INSERT INTO tasks(task_name,priority,assign_to,start_date,end_date,task_desc,status,created_on) VALUES(:task_name,:priority,:assign_to,:start_date,:end_date,:task_desc,:status,:created_on)";
				$result = $conn->prepare($sql);
				$result->execute(array(':task_name'=>$task_name,':priority'=>$priority,':assign_to'=>$assign_to,':start_date'=>$start_date,':end_date'=>$end_date,':task_desc'=>$task_desc,':status'=>$status,':created_on'=>$date));
			}
			else
			{
				$sql = "UPDATE tasks set task_name=:task_name,priority=:priority,assign_to=:assign_to,start_date=:start_date,end_date=:end_date,task_desc=:task_desc,updated_on=:updated_on where task_id=:task_id";
				$result = $conn->prepare($sql);
				$result->execute(array(':task_name'=>$task_name,':priority'=>$priority,':assign_to'=>$assign_to,':start_date'=>$start_date,':end_date'=>$end_date,':task_desc'=>$task_desc,':updated_on'=>$date,':task_id'=>$task_id));
			
			}
			if($result)
				echo 'Saved Successfully';
			else
				echo 'Saved Failed';
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
	else
	{
		echo 'Form Submitted Failed';
	}
}
else if($action=='delete') //.................... Task Delete (change the task status)................
{
	$task_id = isset($_POST['task_id'])?$_POST['task_id']:'';
	try {
			$sql = "UPDATE tasks set status='Backup' where task_id=:task_id";	
			$result = $conn->prepare($sql);
			$result->execute(array(':task_id'=>$task_id));
			if($result)
				echo 'Deleted Successfully';
			else
				echo 'Deleted Failed';
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
}
else if($action=='data')
{
	$stmt = $conn->query("select task.task_id,task.task_name,task.priority,task.start_date,task.end_date,user.user_name from tasks task join users user on user.user_id=task.assign_to where task.status='Active' order by task_id desc");
	$i=1;
	while ($row = $stmt->fetch())
	{ ?>
		<tr>
			<td><?php echo $i++; ?></td>
			<td><?php echo $row['task_name']; ?></td>
			<td><?php echo $row['user_name']; ?></td>
			<td><?php echo $row['priority']; ?></td>
			<td><?php echo date('d-m-Y',strtotime($row['start_date'])); ?></td>
			<td><?php echo date('d-m-Y',strtotime($row['end_date'])); ?></td>
			<td><a class="btn" href="?task_id=<?php echo $row['task_id']; ?>">Edit</a>
			<a class="btn" href="javascript:void(0);" onclick="del_task('<?php echo $row['task_id']; ?>')" >Delete</a>
			</td>
		</tr>
<?php
 }	
}
