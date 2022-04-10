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
						<b>List of Donations</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" id="new_donation">
					<i class="fa fa-plus"></i> New Entry
				</a></span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Date</th>
									<th class="">Donor</th>
									<th class="">Blood Group</th>
									<th class="">Volume (ml)</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$donor = $conn->query("SELECT * FROM donors");
								while($row=$donor->fetch_assoc()){
									$dname[$row['id']] = ucwords($row['name']);
								}
								$donations = $conn->query("SELECT * FROM blood_inventory where status = 1 order by date(date_created) desc ");
								while($row=$donations->fetch_assoc()):
									
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<?php echo date('M d, Y',strtotime($row['date_created'])) ?>
									</td>
									<td class="">
										 <p> <b><?php echo isset($dname[$row['donor_id']]) ? $dname[$row['donor_id']] : 'Donor was removed from the list.' ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo $row['blood_group'] ?></b></p>
									</td>
									<td class="">
										 <p><b><?php echo $row['volume']; ?></b></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-outline-primary edit_donation" type="button" data-id="<?php echo $row['id'] ?>" >Edit</button>
										<button class="btn btn-sm btn-outline-danger delete_donation" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
	
	$('#new_donation').click(function(){
		uni_modal("New donation","manage_donation.php","mid-large")
		
	})
	$('.edit_donation').click(function(){
		uni_modal("Manage donation Details","manage_donation.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_donation').click(function(){
		_conf("Are you sure to delete this donation?","delete_donation",[$(this).attr('data-id')])
	})
	
	function delete_donation($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_donation',
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