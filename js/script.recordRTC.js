il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrVideoInterview = il.Plugins.SrVideoInterview || {};
(function ($, il) {
	il.Plugins.SrVideoInterview = (function ($) {

		var init = function (id) {
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
						type: 'video',
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



