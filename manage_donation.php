<?php 
include 'db_connect.php'; 
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM blood_inventory where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="" id="manage-donation">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="status" value="1">
		<div id="msg"></div>
		<div class="form-group">
			<label for="" class="control-label">Donor's Name</label>
			<select class="custom-select select2" name="donor_id" required>
				<option value=""></option>
				<?php 
				$qry = $conn->query("SELECT * FROM donors order by name asc");
				while($row= $qry->fetch_assoc()):
				?>
				<option value="<?php echo $row['id'] ?>" data-bgroup="<?php echo $row['blood_group'] ?>" <?php echo isset($donor_id) && $donor_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
	        <label for="" class="control-label">Blood Group</label>
			<input type="text" class="form-control" name="blood_group"  value="<?php echo isset($blood_group) ? $blood_group :'' ?>" required readonly>
			
		</div>
		<div class="form-group">
			<label for="" class="control-label">Volume (ml)</label>
			<input type="number" class="form-control text-right" step="any" name="volume"  value="<?php echo isset($volume) ? $volume :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Date of Transfusion/Donation</label>
			<input type="date" class="form-control" name="date_created"  value="<?php echo isset($date_created) ? date('Y-m-d',strtotime($date_created)) :'' ?>" required>
		</div>
	</form>
</div>
<script>
	$('.select2').select2({
		placeholder:'Please select here.',
		width:'100%'
	})
	$(document).ready(function(){
		if('<?php echo isset($donor_id)? 1:0 ?>' == 1)
			$('[name="donor_id"]').trigger('change')	
	})

	
	$('[name="donor_id"]').change(function(){
		var _id = $(this).val()
		if(_id > 0){
			$('[name="blood_group"]').val($(this).find('option[value="'+_id+'"]').attr('data-bgroup'))
		}else{
			$('[name="blood_group"]').val('')
		}
	})
	$('#manage-donation').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_donation',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved.",'success')
						setTimeout(function(){
							location.reload()
						},1000)
				}
			}
		})
	})
</script>