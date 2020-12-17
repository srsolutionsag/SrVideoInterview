il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrVideoInterview = il.Plugins.SrVideoInterview || {};
(function ($, il) {

	/**
	 * @TODO: improve performance by using the same instance when retaking a record.
	 *
	 * @type {{init: init}}
	 */
	il.Plugins.SrVideoInterview = (function ($) {

		/**
		 * VideoRecorderInput
		 *
		 * @param {string} id
		 * @param {string} settings
		 */
		let init = function (id, settings) {
			// convert settings back to an object
			settings = Object.assign(JSON.parse(settings));

			// obtain the plain HTMLMediaElement (hence no jQuery)
			let videoContainer = document.querySelector('video');

			// obtain recording controls
			let btnStart  = $(`#${id} .btn-start-recording`),
					btnStop   = $(`#${id} .btn-stop-recording`),
					btnRetake = $(`#${id} .btn-retake-recording`),
					fileInput = $(`#${id} .resource-id`);

			// init globally accessible vars
			let videoRecorder, mediaStream;

			// register recording start
			btnStart.click(function(e) {
				e.preventDefault();
				startRecording();
			});

			// register recording stop
			btnStop.click(function(e) {
				e.preventDefault();
				stopRecording();
			});

			// register recording retake
			btnRetake.click(async function(e) {
				e.preventDefault();
				await removeVideo(fileInput.val());
				fileInput.val(null);

				startRecording();
			});

			/**
			 * initializes and starts the recording.
			 */
			function startRecording() {
				// manage controls visibility
				btnStart.attr('disabled', true);
				btnStop.removeAttr('disabled');

				// ask for mediaDevices and retrieve their MediaStream(s)
				navigator.mediaDevices.getUserMedia({
					audio: true,
					video: true,
				}).then(function(stream) {
					mediaStream = stream;

					// enable live preview in videoContainer
					videoContainer.style.display = "block";
					videoContainer.volume = 0;
					videoContainer.muted 	= true;
					videoContainer.srcObject = mediaStream;

					// initialize recording
					videoRecorder = new RecordRTC(mediaStream, {
						recorderType: MediaStreamRecorder,
						mimeType: 'video/webm',
						disableLogs: true,
					});

					videoRecorder.startRecording();
					videoRecorder.camera = mediaStream;
				}).catch(function(err) {
					alert("Whoops! Something went wrong, check your console for more details.");
					console.log(err);
				});
			}

			/**
			 * stops the recording and uploads it asynchronously.
			 */
			function stopRecording() {
				// manage controls visibility
				btnStop.attr('disabled', true);

				videoRecorder.stopRecording(async function() {
					// disable live preview in videoContainer
					videoContainer.srcObject = null;
					videoContainer.volume = 1;
					videoContainer.muted 	= false;

					// convert recording to mp4
					// @TODO: prove if mp4 actually works
					let video = new File(
						[videoRecorder.getBlob()],
						'video_' + id + '.mp4',
						{
							type: 'video/mp4'
						}
					);

					let formData = new FormData();
							formData.append('video-blob', video)
							formData.append('video-filename', video.name);

					await uploadVideo(formData);

					// destroy recorder
					videoRecorder.camera.stop();
					videoRecorder.destroy();
					videoRecorder = null;

					// enable retake control
					btnRetake.css('display', 'inline-block');
					btnRetake.removeAttr('disabled');
				});
			}

			/**
			 * uploads a recorded video into the storage service and retrieves the id.
			 *
			 * @param {FormData} video
			 */
			async function uploadVideo(video) {
				return $.ajax({
					url: settings.upload_url,
					data: video,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: function(response) {
						response = Object.assign(JSON.parse(response));
						videoContainer.src = URL.createObjectURL(videoRecorder.getBlob());
						fileInput.val(response[settings.file_identifier_key]);
					},
					error: function (err) {
						alert("Whoops! Something went wrong, check your console for more details.");
						console.log(err);
					}
				});
			}

			/**
			 *
			 * @param {string} videoId
			 * @returns {bool|void}
			 */
			async function removeVideo(videoId) {
				return $.ajax({
					url: settings.removal_url,
					data: {
						[settings.file_identifier_key]: videoId,
					},
					type: 'GET',
					success: function(response) {
						response = Object.assign(JSON.parse(response));
						console.log(response);
					},
					error: function (err) {
						alert("Whoops! Something went wrong, check your console for more details.");
						console.log(err);
					}
				});
			}
		};

		return {
			init: init,
		};
	})($);
})($, il);



