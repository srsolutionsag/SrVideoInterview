il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrVideoInterview = il.Plugins.SrVideoInterview || {};
(function ($, il) {
	il.Plugins.SrVideoInterview = (function ($) {

		/*var initMinial = function (id, video_recorder_url) {
			const constraints = {
				video: true,
				audio: true,
			};


			function handleSuccess(stream) {
				console.log(stream);
				const video = document.querySelector('video');
				const videoTracks = stream.getVideoTracks();
				console.log('Got stream with constraints:', constraints);
				console.log(`Using video device: ${videoTracks[0].label}`);
				window.stream = stream; // make variable available to browser console
				video.srcObject = stream;
			}

			function handleError(error) {
				if (error.name === 'ConstraintNotSatisfiedError') {
					const v = constraints.video;
					errorMsg(`The resolution ${v.width.exact}x${v.height.exact} px is not supported by your device.`);
				} else if (error.name === 'PermissionDeniedError') {
					errorMsg('Permissions have not been granted to use your camera and ' +
						'microphone, you need to allow the page access to your devices in ' +
						'order for the demo to work.');
				}
				errorMsg(`getUserMedia error: ${error.name}`, error);
			}

			function errorMsg(msg, error) {
				const errorElement = document.querySelector('#errorMsg');
				errorElement.innerHTML += `<p>${msg}</p>`;
				if (typeof error !== 'undefined') {
					console.error(error);
				}
			}


			try {
				navigator.mediaDevices.getUserMedia(constraints).then((stream) => handleSuccess(stream));
			} catch (e) {
				handleError(e);
			}

		}*/

		var init = function (id, video_recorder_url) {
			console.log('selecting should be done with the ID of the composnent: ' + id);
			console.log('recorder url: ' + video_recorder_url)
			var video = document.querySelector('video');

			function captureCamera(callback) {
				navigator.mediaDevices.getUserMedia({audio: true, video: true}).then(function (camera) {
					callback(camera);
				}).catch(function (error) {
					alert('Unable to capture your camera. Please check console logs.');
					console.error(error);
				});
			}

			function stopRecordingCallback() {
				video.src = video.srcObject = null;
				video.muted = false;
				video.volume = 1;

				var formData = new FormData();
				formData.append('data', recorder.getBlob());

				/*3 $.ajax({
				   url: '/ilias.php?ref_id=76&cmd=data&cmdClass=ilobjsrvideointerviewgui&cmdNode=101:16f&baseClass=ilobjplugindispatchgui',
				   data: formData,
				   type: 'POST',
				   contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
				   processData: false, // NEEDED, DON'T OMIT THIS
				   // ... Other options like success and etc
				 });*/


				var xhr = new XMLHttpRequest();
				xhr.open('POST', '/ilias.php?ref_id=76&cmd=data&cmdClass=ilobjsrvideointerviewgui&cmdNode=101:16f&baseClass=ilobjplugindispatchgui', true);
				xhr.send(formData);


				video.src = URL.createObjectURL(recorder.getBlob());

				recorder.camera.stop();
				recorder.destroy();
				recorder = null;
			}

			var recorder; // globally accessible

			document.getElementById('btn-start-recording').onclick = function () {
				this.disabled = true;
				captureCamera(function (camera) {
					video.muted = true;
					video.volume = 0;
					video.srcObject = camera;

					recorder = RecordRTC(camera, {
						// type: 'video',
						// mimeType: 'video/mp4'
					});

					recorder.startRecording();

					// release camera on stopRecording
					recorder.camera = camera;

					document.getElementById('btn-stop-recording').disabled = false;
				});
			};

			document.getElementById('btn-stop-recording').onclick = function () {
				this.disabled = true;
				recorder.stopRecording(stopRecordingCallback);
			};


			//
		};

		return {
			init: init,
		};
	})($);
})($, il);



