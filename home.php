<?php include 'db_connect.php' ?>
<style>
   span.float-right.summary_icon {
    font-size: 3rem;
    position: absolute;
    right: 1rem;
    top: 0;
}
.imgs{
		margin: .5em;
		max-width: calc(100%);
		max-height: calc(100%);
	}
	.imgs img{
		max-width: calc(100%);
		max-height: calc(100%);
		cursor: pointer;
	}
	#imagesCarousel,#imagesCarousel .carousel-inner,#imagesCarousel .carousel-item{
		height: 60vh !important;background: black;
	}
	#imagesCarousel .carousel-item.active{
		display: flex !important;
	}
	#imagesCarousel .carousel-item-next{
		display: flex !important;
	}
	#imagesCarousel .carousel-item img{
		margin: auto;
	}
	#imagesCarousel img{
		width: auto!important;
		height: auto!important;
		max-height: calc(100%)!important;
		max-width: calc(100%)!important;
	}
</style>

<div class="containe-fluid">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
                    <hr>
                    <h4><b>Available Blood per group in Liters</b></h4>
                    <div class="row">
                        <?php 
                        $blood_group = array("A+","B+","O+","AB+","A-","B-","O-","AB-");
                        foreach($blood_group as $v){
                            $bg_inn[$v] = 0; 
                            $bg_out[$v] = 0; 
                        }
                        $qry = $conn->query("SELECT * FROM blood_inventory ");
                        while($row = $qry->fetch_assoc()){
                            if($row['status'] == 1){
                                $bg_inn[$row['blood_group']] += $row['volume'];
                            }else{
                                $bg_out[$row['blood_group']] += $row['volume'];
                            }
                        }

                        ?>
                        <?php foreach ($blood_group as $v): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="card-body text-dark">
                                        <span class="float-right summary_icon"> <?php echo $v ?> <i class="fa fa-tint text-danger"></i></span>
                                        <h4><b>
                                            <?php echo ($bg_inn[$v] - $bg_out[$v]) / 1000 ?>
                                        </b></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>	
                    <hr>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="card-body text-dark">
                                        <span class="float-right summary_icon"> <i class="fa fa-user-friends text-primary "></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM donors")->num_rows ?>
                                        </b></h4>
                                        <p><b>Total Donors</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="card-body text-dark">
                                        <span class="float-right summary_icon"> <i class="fa fa-notes-medical text-danger "></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM blood_inventory where status = 1 and date(date_created) = '".date('Y-m-d')."' ")->num_rows ?>
                                        </b></h4>
                                        <p><b>Total Donated Today</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="card-body text-dark">
                                        <span class="float-right summary_icon"> <i class="fa fa-th-list "></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM requests where date(date_created) = '".date('Y-m-d')."' ")->num_rows ?>
                                        </b></h4>
                                        <p><b>Today's Requests</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="card-body text-dark">
                                        <span class="float-right summary_icon"> <i class="fa fa-check text-primary "></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM requests where date(date_created) = '".date('Y-m-d')."' and status = 1 ")->num_rows ?>
                                        </b></h4>
                                        <p><b>Today's Approved Requests</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>      			
        </div>
    </div>
</div>
<script>
	$('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)

                }
                
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
            get_person()
    })
    function get_person(){
            start_load()
        $.ajax({
                url:'ajax.php?action=get_pdetails',
                method:"POST",
                data:{tracking_id : $('#tracking_id').val()},
                success:function(resp){
                    if(resp){
                        resp = JSON.parse(resp)
                        if(resp.status == 1){
                            $('#name').html(resp.name)
                            $('#address').html(resp.address)
                            $('[name="person_id"]').val(resp.id)
                            $('#details').show()
                            end_load()

                        }else if(resp.status == 2){
                            alert_toast("Unknow tracking id.",'danger');
                            end_load();
                        }
                    }
                }
            })
    }
</script>