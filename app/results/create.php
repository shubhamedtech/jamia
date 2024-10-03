<?php require '../../includes/db-config.php';  ?>

	<!-- Modal -->
	<link rel="stylesheet" href="../../assets/plugins/dropify/dropify.min.css">
	<div class="modal-header clearfix text-left">
		<h5>Add Result </h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	
	<form role="form" id="form-add-results" action="/app/results/store"  method="POST" enctype="multipart/form-data">
		<div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-default required">
						<label>Program/Course *</label>
						<select class="form-control" id="course_id" name="course_id" onchange="getStudent(this.value)">
							<option value="">Select</option>
							<?php
								$programs = $conn->query("SELECT ID,Name,Short_Name FROM Courses");
								while ($program = $programs->fetch_assoc()) { ?>
								<option value="<?=$program['ID']?>">
									<?=$program['Name'].' ('.$program['Short_Name'].')'?>
								</option>
								<?php } ?>
						</select>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group form-group-default required">
						<label>Student *</label>
						<select class="form-control" id="student_id" name="student_id" onchange="getSubjects(this.value)">
							<option value="">Select</option>
						</select>
					</div>
				</div>
			</div>

			<div class="student_section modal-body">
    		</div>
			
			<div class="row justify-content-center">
				<button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left"> <i class="ti-save mr-2"></i>Save</button>
				</button>
			</div>
	</form>


	<script src="../../assets/plugins/dropify/dropify.min.js"></script>
	<script>
	$('.dropify').dropify();

	$(function() {
		$('#form-add-e-book').validate({
			rules: {
				course_id: {
					required: true
				},
				// subject_id: {
				// 			required: true
				// 		},
				// file: {
				// required: true
				// },
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



	$("#form-add-results").on("submit", function(e) {
        if ($('#form-add-results').valid()) {
            var formData = new FormData(this);
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                success: function(data) {

                    if (data.status == 200) {
                        notification('success', data.message);
                        $('.modal').modal('hide');
                        $('#results-table').DataTable().ajax.reload();
                    } else {
                        notification('danger', data.message);
                    }


                },
                error: function(data) {
                    notification('danger', 'Server is not responding. Please try again later');
                }
            });
        } else {
            //notification('danger', 'Invalid form information.');
        }
    });
	
	

	function getStudent(course_id) {
        $.ajax({
            url: '/app/results/students',
            type: 'POST',
            dataType: 'text',
            data: {
                course_id: course_id
            },
            success: function(result) {
                $('#student_id').html(result);
            }
        })
    }


	function getSubjects(student_id) {
        var course_id = $("#course_id").val();
        $.ajax({
            url: '/app/results/get-subjects',
            type: 'POST',
            dataType: 'text',
            data: {
                course_id: course_id,
                student_id: student_id,
            },
            success: function(result) {
                $('.student_section').html(result);
            }
        })
    }


	</script>