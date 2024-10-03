<?php require '../../includes/db-config.php';
$videos = $conn->query("SELECT * FROM video_lectures WHERE ID = '" . $_GET['id'] . "'");
$video = mysqli_fetch_assoc($videos);
// print_r($video);
?>
<!-- Modal -->
<link rel="stylesheet" href="../../assets/plugins/dropify/dropify.min.css">
<div class="modal-header clearfix text-left">
	<h5>Upload Video </h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-add-e-book" action="/app/videos/update" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<input type="hidden" name="id" value="<?= $video['id'] ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Program/Course *</label>
					<select class="form-control" id="department" name="course_id" onchange="getSubjects(this.value)">
						<option value="">Select</option>
						<?php
						$programs = $conn->query("SELECT ID,Name,Short_Name FROM Courses");
						while ($program = $programs->fetch_assoc()) { ?>
							<option value="<?= $program['ID'] ?>" <?php print $video['course_id'] == $program['ID'] ? 'selected' : '' ?>>
								<?= $program['Name'] . ' (' . $program['Short_Name'] . ')' ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Semester/Year </label>
					<input type="text" class="form-control" id="semester" name="semester" value="<?php print $video['semester'] ?>" placeholder="Enter Semester">
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
							<option value="<?= $subject['ID'] ?>" <?php print $video['subject_id'] == $subject['ID'] ? 'selected' : '' ?>>
								<?= $subject['name'] ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group form-group-default required">
					<label>Title/Unit *</label>
					<input type="text" class="form-control" name="unit" value="<?php print $video['unit'] ?>" placeholder="Enter Title" id="title">
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group form-group-default required">
				<label>Videos Categories*</label>
				<select class="form-control" name="videos_categories">
					<option value="1" <?php if ($video['Videos_Categories'] == '1') { ?> selected="selected" <?php } ?>>Live Lectures</option>
					<option value="2" <?php if ($video['Videos_Categories'] == '2') { ?> selected="selected" <?php } ?>>Recorded Lectures</option>
				</select>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group form-group-default required">
				<label>Language Categories*</label>
				<select class="form-control" name="language_categories">
					<option value="1" <?php if ($video['Languages_Categories'] == '1') { ?> selected="selected" <?php } ?>>Malayalam</option>
					<option value="2" <?php if ($video['Languages_Categories'] == '2') { ?> selected="selected" <?php } ?>>Tamil</option>
					<option value="3" <?php if ($video['Languages_Categories'] == '3') { ?> selected="selected" <?php } ?>>Arabic</option>
					<option value="4" <?php if ($video['Languages_Categories'] == '4') { ?> selected="selected" <?php } ?>>Kannada</option>
				</select>
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-12">
				<div class="form-group form-group-default required">
					<label>Desciption </label>
					<textarea name="description" class="form-control" rows="4"><?php print $video['description']; ?></textarea>
				</div>
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-12">
				<div class="form-group form-group-default required">
					<label for="mvideos">
						<input type="radio" name="video" id="mvideos" value="mp4" <?php if ($video['video_type'] == 'mp4') {
																						echo 'checked';
																					} ?>> Manual Upload
					</label>
					<br>
					<label for="vurl">
						<input type="radio" name="video" id="vurl" value="url" <?php if ($video['video_type'] == 'url') {
																					echo 'checked';
																				} ?>> YouTube Link
						<br>
						<a href="<?php echo $video['video_url']; ?>"><span>View Video</span></a>
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
				<input type="file" name="video_file" style="display:none;" id="v" class="form-control" accept="video/*">
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-12">
				<label>Thumbnail </label>
				<input type="file" name="thumbnail" class="dropify" accept="image/png, image/jpg, image/jpeg, image/svg">
				<a target="_blank" href="<?php print $video['thumbnail_url']; ?>"><span>View Thumbnail File</span></a>
			</div>
		</div>

		<div class="row justify-content-center">
			<button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left"> <i class="ti-save mr-2"></i>Update</button>
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
				//$(element).addClass('error');
				$(element).closest('.form-control').addClass('has-error');
			},
			unhighlight: function(element) {
				//$(element).removeClass('error');
				$(element).closest('.form-control').removeClass('has-error');
			}
		});
	});

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
						$('#video_lectures-table').DataTable().ajax.reload(null, false);
					} else {
						notification('danger', data.message);
					}
				},
				error: function(data) {
					console.log(data);
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
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script> -->
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