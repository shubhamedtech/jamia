<?php require '../../includes/db-config.php';  ?>
<!-- Modal -->
<link rel="stylesheet" href="/assets/plugins/dropify/dropify.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<div class="modal-header clearfix text-left">
	<h5>Upload Video </h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-add-e-book" action="/app/videos/store" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Program/Course *</label>
					<select class="form-control" id="department" name="course_id" onchange="getSubjects(this.value)">
						<option value="">Select</option>
						<?php
						$programs = $conn->query("SELECT ID,Name,Short_Name FROM Courses");
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
					<label>Semester/Year </label>
					<input type="text" class="form-control" id="semester" name="semester" placeholder="Enter Semester">
				</div>
			</div>

		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Subject *</label>
					<select class="form-control" id="subject_id" name="subject_id">
						<option value="">Select</option>
						<?php
						$subjects = $conn->query("SELECT ID,name FROM Subjects");
						while ($subject = $subjects->fetch_assoc()) { ?>
							<option value="<?= $subject['ID'] ?>">
								<?= $subject['name'] ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Title/Unit *</label>
					<input type="text" class="form-control" name="unit" placeholder="Enter Title" id="title">
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group form-group-default required">
				<label>Videos Categories *</label>
				<select class="form-control" id="videos_categories" name="videos_categories">
					<option value="1">Live Lectures</option>
					<option value="2">Recorded Videos</option>
				</select>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group form-group-default required">
				<label>Choose Language Types*</label>
				<select class="form-control" id="language_categories" name="language_categories">
					<option value="1">Malayalam</option>
					<option value="2">Tamil</option>
					<option value="3">Arabic</option>
					<option value="4">Kannada</option>
				</select>
			</div>
		</div>

		<div class="row mb-2">
			<div class="col-md-12">
				<div class="form-group form-group-default required">
					<label>Desciption</label>
					<textarea name="description" class="form-control" rows="4"></textarea>
				</div>
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-12">
				<div class="form-group form-group-default required">
					<label for="mvideos">
						<input type="radio" name="videos" id="mvideos"> Manual Uploads
					</label>
					<label for="vurl">
						<input type="radio" name="videos" id="vurl"> YouTube Link
					</label>
				</div>
			</div>
		</div>

		<div class="row mb-2">
			<div class="col-md-12">
				<input type="url" name="uniform" id="u" class="form-control" placeholder="YouTube Link Url" style="display:none;">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-12">
				<input type="file" name="video" style="display:none;" id="v" class="form-control" accept="video/*">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-12">
				<label>Thumbnail *</label>
				<input type="file" name="thumbnail" class="dropify" accept="image/png, image/jpg, image/jpeg, image/svg">
			</div>
		</div>


		<div class="row justify-content-center">
			<button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left"><i class="ti-save mr-2"></i>Upload</button>
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
				thumbnail: {
					required: true
				},
				video: {
					required: true
				},
				unit: {
					required: true
				},
				videos_categories: {
					required: true
				},
				semester: {
					required: true
				},
				vurl: {
					required: true
				},
			},
			highlight: function(element) {
				$(element).closest('.form-control').addClass('has-error');
			},
			unhighlight: function(element) {
				$(element).closest('.form-control').removeClass('has-error');
			}
		});
	})
	$('#form-add-e-book').submit(function(e) {
		e.preventDefault();
		if ($('#form-add-e-book').valid()) {
			var formData = new FormData(this);
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
						$('#video_lectures-table').DataTable().ajax.reload(null, false);
					} else {
						notification('danger', data.message);
					}
				},
				error: function(data) {
					notification('danger', 'Server is not responding. Please try again later');
				}
			});
		} else {}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script>
	$(document).ready(function() {
		$("#mvideos").click(function() {
			$("#v").show();
			$("#u").hide();
		});
		$("#vurl").click(function() {
			$("#u").show();
			$("#v").hide();
		});
	});
</script>