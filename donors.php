<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>List of Donors</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" id="new_donor">
					<i class="fa fa-plus"></i> New Entry
				</a></span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Donor</th>
									<th class="">Blood Group</th>
									<th class="">Information</th>
									<th class="">Previuos Donation</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$donors = $conn->query("SELECT * FROM donors order by name asc ");
								while($row=$donors->fetch_assoc()):
									$prev  = $conn->query("SELECT * FROM blood_inventory where status = 1 and donor_id = ".$row['id']." order by date(date_created) desc limit 1 ");
									$prev = $prev->num_rows > 0 ? $prev->fetch_array()['date_created'] : '';	
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										 <p> <b><?php echo ucwords($row['name']) ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo $row['blood_group'] ?></b></p>
									</td>
									<td class="">
										 <p>Email: <b><?php echo $row['email']; ?></b></p>
										 <p>Contact #: <b><?php echo $row['contact']; ?></b></p>
										 <p>Address: <b><?php echo $row['address']; ?></b></p>
									</td>
									<td>
										<?php echo !empty($prev) ? date('M d, Y',strtotime($prev)) : 'New' ?>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-outline-primary edit_donor" type="button" data-id="<?php echo $row['id'] ?>" >Edit</button>
										<button class="btn btn-sm btn-outline-danger delete_donor" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height: :150px;
	}
</style>
<script>
	$(document).ready(function(){
		$('table').dataTable()
	})
	
	$('#new_donor').click(function(){
		uni_modal("New Donor","manage_donor.php","mid-large")
		
	})
	$('.edit_donor').click(function(){
		uni_modal("Manage donor Details","manage_donor.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_donor').click(function(){
		_conf("Are you sure to delete this donor?","delete_donor",[$(this).attr('data-id')])
	})
	
	function delete_donor($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_donor',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>