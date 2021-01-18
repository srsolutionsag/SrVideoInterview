/**
 * (UI) VideoRecorderInput js does the actual recording when using this
 * UIComponent within the plugin.
 *
 * This script initialises a media stream of the clients video and audio devices,
 * after he granted us permission. Via buttons he can then start a recording of
 * this stream, which will record up to 20 minutes max. Before form-submission, the
 * recording will be uploaded to the ILIAS storage service and receive a resource-
 * id and set this to a hidden input field.
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * The technology we use is called webRTC, and can be found by following sources:
 *
 * @see https://webrtc.github.io/samples/src/content/getusermedia/record/
 * @see https://github.com/webrtc/samples/blob/gh-pages/src/content/getusermedia/record/js/main.js
 */
il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrVideoInterview = il.Plugins.SrVideoInterview || {};
(function ($, il) {
	il.Plugins.SrVideoInterview = (function ($) {
		/**
		 * init
		 *
		 * @TODO: refactor this later for readability.
		 * @TODO: add apple safari browser support.
		 *
		 * @param {string} id
		 * @param {string} settings
		 */
		let init = function (id, settings) {
			settings = Object.assign(JSON.parse(settings));
			// console.log(settings);

			let recorder,
					recordedData,
					recordedVideo;

			let videoPreview  = document.querySelector('video.sr-video-preview'),
					submitButton  = $('.il-standard-form-cmd > button'),
					resourceInput = $(`#${id} .sr-resource-input`),
					recordButton  = $(`#${id} .sr-record-btn`),
					retakeButton  = $(`#${id} .sr-retake-btn`),
					errorMessage  = $(`#${id} .sr-error-msg`),
					form 					= resourceInput.form(),
					timer					= 0,
					predecessor	  = null;

			let enableRetakeButton = function() {
				if ('none' === retakeButton.css('display')) {
					retakeButton.css('display', 'inline-block');
				}

				retakeButton.removeAttr('disabled');
			}

			let displayErrorMessage = function(text) {
				if ('none' === errorMessage.css('display')) {
					errorMessage.css('display', 'inline-block');
				}

				errorMessage.find('span').text(text);
			}

			let handleDataAvailable = function(event) {
				if (event.data && event.data.size > 0) {
					recordedData.push(event.data);
				}
			}

			let handleVideoResult = function() {
				recordedVideo = new Blob(
					recordedData,
					{
						type: 'video/webm',
						name: `video_${id}.webm`,
					}
				);

				videoPreview.src = null;
				videoPreview.srcObject = null;
				videoPreview.src = window.URL.createObjectURL(recordedVideo);
				videoPreview.controls = true;
				videoPreview.muted = false;
				videoPreview.play();
			}

			let handleVideoUpload = async function(video) {
				if (predecessor) {
					let error = false;
					await $.ajax({
						url: settings.removal_url,
						data: {
							[settings.file_identifier_key]: predecessor,
						},
						type: 'GET',
						success: function(response) {
							response = Object.assign(JSON.parse(response));
							// console.log(response);
							if (!1 === response.status) {
								displayErrorMessage(settings.lng_vars['general_error']);
								error = true;
							}
						},
						error: function(e) {
							// console.error("Error when deleting video predecessor : ", e)
							displayErrorMessage(settings.lng_vars['general_error']);
							error = true;
						}
					});

					if (error) return;
				}

				let formData = new FormData();
						formData.append('video-blob', video)
						formData.append('video-filename', video.name);

				await $.ajax({
					url: settings.upload_url,
					data: formData,
					contentType: false,
					processData: false,
					type: 'POST',
					success: function(response) {
						response = Object.assign(JSON.parse(response));
						// console.log(response);
						if (1 === response.status) {
							resourceInput.val(response[settings.file_identifier_key]);
							form.submit();
						} else {
							displayErrorMessage(settings.lng_vars['general_error']);
						}
					},
					error: function(e) {
						// console.error("Error when uploading the video blob: ", e)
						displayErrorMessage(settings.lng_vars['general_error']);
					}
				});
			}

			let startRecording = function() {
				recordedData = [];
				submitButton.attr('disabled', true);
				videoPreview.srcObject = window.stream;
				videoPreview.muted = true;
				videoPreview.controls = false;
				videoPreview.play();

				try {
					recorder = new MediaRecorder(window.stream, getSupportedMimeType);
				} catch (e) {
					displayErrorMessage(settings.lng_vars['general_error']);
					return;
				}

				// register event-handler
				recorder.ondataavailable = handleDataAvailable;
				recorder.onstop = handleVideoResult;

				recorder.start();

				// set maximum duration of recording to 20 minutes
				timer = setTimeout(stopRecording, (1000 * 60 * 20));
			}

			let stopRecording = function() {
				recorder.stop();
				if (0 !== timer) {
					clearTimeout(timer);
					timer = 0;
				}
				enableRetakeButton();
				submitButton.removeAttr('disabled');

				// for debugging purposes, stop media-stream on stop
				// window.stream.getTracks().forEach(function(track) {
				// 	track.stop();
				// });
			}

			let handleSuccess = function(stream) {
				window.stream = stream;

				if (resourceInput.val()) {
					enableRetakeButton();
				} else {
					videoPreview.srcObject = stream;
					recordButton.removeAttr('disabled');
				}
			}

			let getMediaStream = async function(constraints) {
				return await navigator.mediaDevices.getUserMedia(constraints);
			}

			let getSupportedMimeType = function() {
				let options = {mimeType: 'video/webm;codecs=vp9,opus'};
				if (!MediaRecorder.isTypeSupported(options.mimeType)) {
					options = {mimeType: 'video/webm;codecs=vp8,opus'};
					if (!MediaRecorder.isTypeSupported(options.mimeType)) {
						options = {mimeType: 'video/webm'};
						if (!MediaRecorder.isTypeSupported(options.mimeType)) {
							options = {mimeType: ''};
						}
					}
				}

				return options;
			}

			recordButton.click(function() {
				if (recordButton.val() === settings.lng_vars['start']) {
					startRecording();
					recordButton.val(settings.lng_vars['stop']);
				} else {
					stopRecording();
					recordButton.attr('disabled', true);
				}
			});

			retakeButton.click(function() {
				startRecording();
				retakeButton.attr('disabled', true);
				recordButton.removeAttr('disabled');
			});

			submitButton.click(async function(e) {
				retakeButton.attr('disabled', true);
				recordButton.attr('disabled', true);

				if (undefined !== recordedVideo) {
					e.preventDefault();
					submitButton.attr('disabled', true);
					
					await handleVideoUpload(recordedVideo);
				}
			});

			$(async function() {
				try {
					const stream = await getMediaStream({
						audio: { echoCancellation: {exact: false} },
						video: { width: 1280, height: 720	}
					});

					handleSuccess(stream);
				} catch (e) {
					// console.error('navigator.getUserMedia error: ', e);
					displayErrorMessage(settings.lng_vars['general_error']);
				}

				if (resourceInput.val()) {
					predecessor = resourceInput.val();
					videoPreview.autoplay = false;
					videoPreview.controls = true;
					videoPreview.muted = false;
					videoPreview.src = `${settings.download_url}&${settings.file_identifier_key}=${predecessor}`;
					recordButton.attr('disabled', true);
					recordButton.val(settings.lng_vars['stop']);
					retakeButton.css('display', 'inline-block');
					retakeButton.removeAttr('disabled');
				}
			});
		};

		return {
			init: init,
		};
	})($);
})($, il);