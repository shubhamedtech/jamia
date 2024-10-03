<?php require '../../includes/db-config.php';  ?>

	<!-- Modal -->
	<link rel="stylesheet" href="../../assets/plugins/dropify/dropify.min.css">
	<div class="modal-header clearfix text-left">
		<h5>Import Result </h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	
	<form role="form" id="form-import-results" action="/app/results/excel-import-server"  method="POST" enctype="multipart/form-data">
		<div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-default required">
						<label>Upload Excel</label>
						<input name="file" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
					</div>
				</div>

                <div class="col-md-6">
                    <div class="form-group form-group-default required">
                        <label></label>
                        <div class="col-md-12 text-end cursor-pointer" onclick="window.open('/app/samples/exam-result');">
                            <i class="fa fa-lg fa-download"></i><u><span class="text-primary ml-1">Download Sample</span></u>
                        </div>
                    </div>
                </div>

			</div>

			<div class="row justify-content-center">
				<button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left" > <i class="ti-save mr-2"></i>Import</button>
				</button>
			</div>
	</form>


	<script src="../../assets/plugins/dropify/dropify.min.js"></script>
	<script>
	$('.dropify').dropify();

	$(function() {
		$('#form-add-e-book').validate({
			rules: {
				
            file: {
            required: true
            },
            
            },
            highlight: function(element) {
                //$(element).addClass('error');
                $(element).closest('.form-control').addClass('has-error');
            },
            unhighlight: function(element) {
                //$(element).removeClass('error');
                $(element).closest('.form-control').removeClass('has-error');
            }
		});
	})

	$('#form-import-results').submit(function(e) {
		$('.modal').modal('hide');
		location.reload();
		//$('#results-table').DataTable().ajax.reload(null, false);
		notification('success', data.message);
	});


	// $('#form-import-results').submit(function(e) { //working code
    // if ($('#form-import-results').valid()) {
	// 		var formData = new FormData(this);
	// 		e.preventDefault();
	// 		$.ajax({
	// 			url: $(this).attr('action'),
	// 			type: "POST",
	// 			data: formData,
	// 			contentType: false,
	// 			cache: false,
	// 			processData: false,
	// 			dataType: 'json',
	// 			success: function(data) {
	// 				if(data.status == 200) {
	// 					notification('success', data.message);
                    
    //                 $('#results-table').DataTable().ajax.reload(null, false);
	// 				} else {
	// 					
	// 					notification('danger', data.message);
	// 				}

	// 				$('.modal').modal('hide');
	// 			},
	// 			error: function(data) {
	// 				notification('danger', 'Server is not responding. Please try again later');
	// 			}
	// 		});
    // }else{
    //   //notification('danger', 'Invalid form information.');
    // }
	// });


	</script>