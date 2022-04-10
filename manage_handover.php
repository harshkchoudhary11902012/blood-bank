<?php 
include 'db_connect.php'; 
if(isset($_GET['id'])){
$qry = $conn->query("SELECT hr.*,r.patient,r.blood_group, r.volume,r.ref_code FROM handedover_request hr inner join requests r on r.id = hr.request_id where hr.id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
    $$k=$val;
}
}
?>
<div class="container-fluid">
    <form action="" id="manage-handover">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg"></div>
        <div class="form-group">
            <label for="" class="control-label">Request's Reference Code</label>
            <input type="text" class="form-control" name="ref_code"  value="<?php echo isset($ref_code) ? $ref_code :'' ?>" required>
        </div>
        <div class="form-group">
            <button class="btn btn-sm btn-primary" type="button" id="chk_request">Check</button>
        </div>
        <div class="form-group" id="request_details">
            
        </div>

        <div class="form-group" style="display: none">
            <label for="" class="control-label">Received by: </label>
            <input type="text" class="form-control" name="picked_up_by"  value="<?php echo isset($picked_up_by) ? $picked_up_by :'' ?>" >
        </div>
</div>
    </form>
</div>
<script>
    $(document).ready(function(){
        if('<?php echo isset($id)? 1:0 ?>' == 1)
             $('#chk_request').trigger('click') 
    })
    $('[name="ref_code"]').keypress(function(e){
        if(e.which == 13){
             $('#chk_request').trigger('click')
             return false;
        }
    })
    $('#chk_request').click(function(){
        var ref_code = $('[name="ref_code"]').val()
        $('#msg').val('')
        $('#request_details').html('')
        $('[name="picked_up_by"]').parent().hide()
        $.ajax({
            url:'ajax.php?action=chk_request',
            method:'POST',
            data:{ref_code:ref_code,id:'<?php echo isset($id) ? $id : '' ?>'},
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp)
                    if(resp.status == 1){
                        var _html = '';
                        _html += '<input type="hidden" name="request_id" value="'+resp.data.id+'">';
                        _html += '<p>Patient: '+resp.data.id+'<b></b></p>';
                        _html += '<p>Blood Group: '+resp.data.blood_group+'<b></b></p>';
                        _html += '<p>Volume Needed: '+resp.data.volumeL+' L<b></b></p>';
                        _html += '<p>Physician: '+resp.data.physician_name+'<b></b></p>';
                        $('#request_details').html(_html)
                        $('[name="picked_up_by"]').parent().show()

                    }else if(resp.status == 2){
                         $('#msg').html('<div class="alert alert-danger">Request is not approved yet.</div>')
                            end_load()
                            return false;
                    }else if(resp.status == 3){
                         $('#msg').html('<div class="alert alert-danger">Request already handed over.</div>')
                            end_load()
                            return false;
                    }else{
                        $('#msg').html('<div class="alert alert-danger">Unknown Reference Code.</div>')
                            end_load()
                            return false;
                    }
                }
            }
        })
    })
    $('#manage-handover').submit(function(e){
        e.preventDefault()
        start_load()
        $('#msg').html('')
        if($('[name="volume"]').val() > $('[name="avolume"]').val()){
            $('#msg').html('<div class="alert alert-danger">Blood volume is greater than available volume.</div>')
            end_load()
            return false;
        }
        $.ajax({
            url:'ajax.php?action=save_handover',
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