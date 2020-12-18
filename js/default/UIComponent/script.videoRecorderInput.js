il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrVideoInterview = il.Plugins.SrVideoInterview || {};
(function ($, il) {
	/**
	 * @TODO: improve performance by using the same instance when retaking a record.
	 * @TODO: fix preview error, that doesn't work when retaking an existing recording.
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
			settings = Object.assign(JSON.parse(settings));
			console.log(settings);

			// obtain the plain HTMLMediaElement (hence no jQuery)
			let videoContainer = document.querySelector('video');

			// get form submit buttons
			let btnsSubmit = $('.il-standard-form-cmd > button');

			// obtain recording controls
			let btnStart  = $(`#${id} .btn-start-recording`),
					btnStop   = $(`#${id} .btn-stop-recording`),
					btnRetake = $(`#${id} .btn-retake-recording`),
					fileInput = $(`#${id} .resource-id`);

			// init globally accessible vars
			let videoRecorder, mediaStream;

			// when retaking an existing, dont delete on first retake.
			let retakeOnExisting = false;

			// check onload if already a recording exists and load it
			$(function() {
				if (fileInput.val()) {
					retakeOnExisting = true;
					videoContainer.style.display = "block";
					videoContainer.volume = 1;
					videoContainer.muted = false;
					videoContainer.autoplay = false;
					videoContainer.src = `${settings.download_url}&${settings.file_identifier_key}=${fileInput.val()}`;
					btnStart.attr('disabled', true);
					btnRetake.css('display', 'inline-block');
					btnRetake.removeAttr('disabled');
					btnsSubmit.removeAttr('disabled');
				}
			});

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
				if (!retakeOnExisting) {
					retakeOnExisting = false;
					await removeVideo(fileInput.val());
				}

				fileInput.val(null);
				btnRetake.attr('disabled', true);
				startRecording();
			});

			/**
			 * initializes and starts the recording.
			 */
			let startRecording = function() {
				// manage controls visibility
				btnStart.attr('disabled', true);
				btnStop.removeAttr('disabled');
				btnsSubmit.attr('disabled', true);

				// ask for mediaDevices and retrieve their MediaStream(s)
				navigator.mediaDevices.getUserMedia({
					audio: true,
					video: true,
				}).then(function(stream) {
					mediaStream = stream;

					// enable live preview in videoContainer
					videoContainer.style.display = "block";
					videoContainer.volume = 0;
					videoContainer.muted = true;
					videoContainer.src = '';
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
			let stopRecording = function() {
				// manage controls visibility
				btnStop.attr('disabled', true);

				videoRecorder.stopRecording(async function() {
					// disable live preview in videoContainer
					videoContainer.srcObject = null;
					videoContainer.volume = 1;
					videoContainer.muted 	= false;

					// prepare blob-data for upload
					let video = new File(
						[videoRecorder.getBlob()],
						'video_' + id + '.webm',
						{
							type: 'video/webm'
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

					// stop media streams (seems to change nothing)
					// mediaStream.getTracks().forEach(function(track) {
					// 	track.stop();
					// });

					mediaStream = null;

					// enable retake control
					btnRetake.css('display', 'inline-block');
					btnRetake.removeAttr('disabled');

					btnsSubmit.removeAttr('disabled');
				});
			}

			/**
			 * uploads a recorded video into the storage service and retrieves the id.
			 *
			 * @param video
			 * @returns {Promise<!jQuery.jqXHR|jQuery>}
			 */
			let uploadVideo = async function(video) {
				return $.ajax({
					url: settings.upload_url,
					data: video,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: function(response) {
						response = Object.assign(JSON.parse(response));
						console.log(response);
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
			 * remove an uploaded recording by its id.
			 *
			 * @param videoId
			 * @returns {Promise<!jQuery.jqXHR|jQuery>}
			 */
			let removeVideo = async function(videoId) {
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