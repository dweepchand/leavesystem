<?php
require('top.inc.php');

if(isset($_GET['type']) && $_GET['type']=='delete' && isset($_GET['id'])){
	$id=mysqli_real_escape_string($con,$_GET['id']);
	mysqli_query($con,"DELETE FROM `leave` WHERE id='$id'");
}

if(isset($_GET['type']) && $_GET['type']=='update' && isset($_GET['id'])){
	$id=mysqli_real_escape_string($con,$_GET['id']);
	$status=mysqli_real_escape_string($con,$_GET['status']);
	$approved_by = $_SESSION['USER_ID']; // The ID of the user making the approval
	mysqli_query($con,"UPDATE `leave` SET leave_status='$status', approved_by='$approved_by' WHERE id='$id'");
	mysqli_query($con,"INSERT INTO `leave_approval_history` (leave_id, approver_id, approval_status) VALUES ('$id', '$approved_by', '$status')");
}

if($_SESSION['ROLE']==1){
	$sql="SELECT `leave`.*, employee.name, employee.id as eid, approver.name as approver_name
		  FROM `leave`
		  JOIN employee ON `leave`.employee_id = employee.id
		  LEFT JOIN employee AS approver ON `leave`.approved_by = approver.id
		  ORDER BY `leave`.id DESC";
}else{
	$eid=$_SESSION['USER_ID'];
	$sql="SELECT `leave`.*, employee.name, employee.id as eid, approver.name as approver_name
		  FROM `leave`
		  JOIN employee ON `leave`.employee_id = employee.id
		  LEFT JOIN employee AS approver ON `leave`.approved_by = approver.id
		  WHERE `leave`.employee_id='$eid'
		  ORDER BY `leave`.id DESC";
}
$res=mysqli_query($con,$sql);
?>
<div class="content pb-0">
	<div class="orders">
		<div class="row">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body">
						<h4 class="box-title">Leave </h4>
						<?php if($_SESSION['ROLE']==2){ ?>
						<h4 class="box_title_link"><a href="add_leave.php">Add Leave</a></h4>
						<?php } ?>
					</div>
					<div class="card-body--">
						<div class="table-stats order-table ov-h">
							<table class="table">
								<thead>
									<tr>
										<th width="5%">S.No</th>
										<th width="5%">ID</th>
										<th width="15%">Employee Name</th>
										<th width="14%">From</th>
										<th width="14%">To</th>
										<th width="15%">Description</th>
										<th width="18%">Leave Status</th>
										<th width="10%">Approved By</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i=1;
									while($row=mysqli_fetch_assoc($res)){
										$leave_id = $row['id'];
										$approval_history_res = mysqli_query($con, "SELECT approver.name AS approver_name, leave_approval_history.approval_status
											FROM leave_approval_history
											JOIN employee AS approver ON leave_approval_history.approver_id = approver.id
											WHERE leave_approval_history.leave_id = '$leave_id'");
										$approval_history = [];
										while($history_row = mysqli_fetch_assoc($approval_history_res)){
											$approval_history[] = $history_row['approver_name'] . ' (' . ($history_row['approval_status'] == 2 ? 'Approved' : 'Rejected') . ')';
										}
										$approval_history_str = implode(', ', $approval_history);
										?>
									<tr>
										<td><?php echo $i?></td>
										<td><?php echo $row['id']?></td>
										<td><?php echo $row['name'].' ('.$row['eid'].')'?></td>
										<td><?php echo $row['leave_from']?></td>
										<td><?php echo $row['leave_to']?></td>
										<td><?php echo $row['leave_description']?></td>
										<td>
											<?php
											if($row['leave_status']==1){
												echo "Applied";
											}if($row['leave_status']==2){
												echo "Approved";
											}if($row['leave_status']==3){
												echo "Rejected";
											}
											?>
											<?php if($_SESSION['ROLE']==1){ ?>
											<select class="form-control" onchange="update_leave_status('<?php echo $row['id']?>',this.options[this.selectedIndex].value)">
												<option value="">Update Status</option>
												<option value="2">Approved</option>
												<option value="3">Rejected</option>
											</select>
											<?php } ?>
										</td>
										<td>
											<?php echo $approval_history_str ? $approval_history_str : 'N/A'; ?>
										</td>
										<td>
										<?php
										if($_SESSION['ROLE']==1 && $row['leave_status']==1){ ?>
										<a href="leave.php?id=<?php echo $row['id']?>&type=delete">Delete</a>
										<?php } ?>
										</td>
									</tr>
									<?php
									$i++;
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function update_leave_status(id,select_value){
	window.location.href='leave.php?id='+id+'&type=update&status='+select_value;
}
</script>
<?php
require('footer.inc.php');
?>
