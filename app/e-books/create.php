<?php require '../../includes/db-config.php';  ?>

<!-- Modal -->
<link rel="stylesheet" href="../../assets/plugins/dropify/dropify.min.css">
<div class="modal-header clearfix text-left">
	<h5>Upload E-Book </h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>

<form role="form" id="form-add-e-book" action="/app/e-books/store" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Program/Course *</label>
					<select class="form-control" id="department" name="course_id" onchange="getSubjects(this.value)">
						<option value="">Select</option>
						<?php
						$programs = $conn->query("SELECT ID,Name,Short_Name FROM Courses WHERE Status=1 ORDER BY Name ASC");
						while ($program = $programs->fetch_assoc()) { ?>
							<option value="<?= $program['ID'] ?>">
								<?= $program['Name'] . ' (' . $program['Short_Name'] . ')' ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Subject *</label>
					<select class="form-control" id="subject_id" name="subject_id">
						<option value="">Select</option>
						<?php
						$subjects = $conn->query("SELECT ID,Name FROM Subjects ORDER BY Name ASC");
						while ($subject = $subjects->fetch_assoc()) { ?>
							<option value="<?= $subject['ID'] ?>">
								<?= $subject['Name'] ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group form-group-default required">
					<label> E-book Name</label>
					<input type="text" name="title" class="form-control" placeholder="Enter E-book Name..." required>
				</div>
			</div>
			<div class="col-md-12">
				<label>E-book file *</label>
				<input type="file" name="file" class="dropify" accept="pdf">
			</div>
		</div>

		<div class="mt-3 row justify-content-center">
			<button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left"> <i class="ti-save mr-2"></i>Upload</button>
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
				subject_id: {
					required: true
				},
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


	$('#form-add-e-book').submit(function(e) {
		if ($('#form-add-e-book').valid()) {
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
					} else {
						notification('danger', data.message);
					}
					$('#e_books-table').DataTable().ajax.reload(null, false);
				},
				error: function(data) {
					notification('danger', 'Server is not responding. Please try again later');
				}
			});
		} else {
			//notification('danger', 'Invalid form information.');
		}
	});

	function getSubjects(course_id) {
		$.ajax({
			url: '/app/videos/subjects',
			type: 'POST',
			dataType: 'text',
			data: {
				'course_id': course_id
			},
			success: function(result) {
				$('#subject_id').html(result);
			}
		})
	}
</script>