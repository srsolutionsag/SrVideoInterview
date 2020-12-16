il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrVideoInterview = il.Plugins.SrVideoInterview || {};
(function ($, il) {

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
			console.log(settings);

			// obtain the plain HTMLMediaElement (hence no jQuery)
			let videoContainer = document.querySelector('video');

			// obtain recording controls
			let btnStart = $(`#${id} #btn-start-recording`),
					btnStop  = $(`#${id} #btn-stop-recording`);

			// init globally accessible vars
			let videoRecorder, mediaStream;

			// register recording start
			btnStart.click(function(e) {
				e.preventDefault();
				toggleRecordingControls();
				startRecording();
			});

			// register recording stop
			btnStop.click(function(e) {
				e.preventDefault();
				toggleRecordingControls();
				stopRecording();
			});

			/**
			 * helper function to enable stop button and disable start when clicked and vise-versa.
			 */
			function toggleRecordingControls() {
				if (!btnStart.prop('disabled')) {
					btnStart.attr('disabled', true);
					btnStop.removeAttr('disabled');
				} else {
					btnStart.removeAttr('disabled');
					btnStop.attr('disabled', true);
				}
			}

			/**
			 * initializes and starts the recording.
			 *
			 * @TODO: outsource to different method stubs.
			 */
			function startRecording() {
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
			 * stops the recording and handles the upload of it.
			 *
			 * @TODO: outsource to different method stubs.
			 */
			function stopRecording() {
				videoRecorder.stopRecording(async function() {
					// disable live preview in videoContainer
					videoContainer.srcObject = null;
					videoContainer.volume = 1;
					videoContainer.muted 	= false;

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

					// upload recorded video asynchronously
					await uploadVideo(formData);

					// we should either download recorded video into container and set hidden-input value
					// or should call another ajax request which removes the video from the storage service here.

					// destroy recorder
					videoRecorder.camera.stop();
					videoRecorder.destroy();
					videoRecorder = null;
				});
			}

			/**
			 * uploads a recorded video into the storage service and retrieves the id.
			 *
			 * @param {FormData} video
			 * @returns {string|void} fileId
			 */
			async function uploadVideo(video) {
				return $.ajax({
					url: settings.upload_url,
					data: video,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: async function(response) {
						response = Object.assign(JSON.parse(response));

						/**
						 * @TODO: we should either download recorded video into container and set hidden-input value
						 * 				or should call another ajax request which removes the video from the storage service here.
						 */
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



