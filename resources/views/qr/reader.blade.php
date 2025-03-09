<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div id="wrapper" class="relative h-auto w-auto">
            <video id="video" autoplay muted playsinline class="absolute top-0 left-0 invisible"></video>
            <canvas id="camera-canvas" class="absolute top-0 left-0 z-20"></canvas>
            <canvas id="rect-canvas" class="absolute top-0 left-0 z-50"></canvas>
            <svg class="absolute top-0 left-0 w-full h-full z-50">
                <rect id="target-rect" class="z-50" x="100" y="60" rx="20" ry="20" width="400" height="400"
                storoke-opacity="0.5" stroke-dasharray="10px" stroke="gray" fill="none" stroke-width="5">
            </svg>
        </div>
    </div>

    <script src="./jsQR.js"></script>
    <script>
        // Zoom Ratio
        const ratio = 2;
        // Webカメラの起動
        const video = document.getElementById('video');
        let contentWidth;
        let contentHeight;
        let zoomWidth;
        let zoomHeight;

        const media = navigator.mediaDevices.getUserMedia({
            audio: false, video: {width:640, height:480}
        })
        .then((stream) => {
            video.srcObject = stream;
            video.onloadeddata = () => {
                video.play();
                contentWidth = video.clientWidth;
                contentHeight = video.clientHeight;
                zoomWidth = contentWidth / ratio;
                zoomHeight = contentHeight / ratio;
                canvasUpdate();
                checkImage();
            }
        }).catch((e) => {
            console.log(e);
        });
        // カメラ映像のキャンバス表示
        const cvs = document.getElementById('camera-canvas');
        const ctx = cvs.getContext('2d', {willReadFrequently:true});
        const tgtrect = document.getElementById('target-rect');

        const wrapper = document.getElementById('wrapper');
        const canvasUpdate = () => {
            cvs.width = contentWidth;
            cvs.height = contentHeight;
            wrapper.style.height=contentHeight + 'px';
            wrapper.style.width=contentWidth + 'px';
            ctx.drawImage(video, 0, 0, contentWidth, contentHeight);
            requestAnimationFrame(canvasUpdate);

            // 拡大の枠描画用
            tgtrect.style.x = (contentWidth - zoomWidth) / 2;
            tgtrect.style.y = (contentHeight - zoomHeight) / 2;
            tgtrect.style.width = zoomWidth;
            tgtrect.style.height = zoomHeight;

        }

        // QRコードの検出
        const rectCvs = document.getElementById('rect-canvas');
        const rectCtx =  rectCvs.getContext('2d');
        const checkImage = () => {
        // imageDataを作る
        const imageData = ctx.getImageData(0, 0, contentWidth, contentHeight);
        // jsQRに渡す
        const code = jsQR(imageData.data, contentWidth, contentHeight);

        // 検出結果に合わせて処理を実施
        if (code) {
            console.log("QRcodeが見つかりました", code);
            drawRect(code.location);
        } else {
            console.log("QRcodeが見つかりません…", code);
            rectCtx.clearRect(0, 0, contentWidth, contentHeight);
        }
        setTimeout(()=>{ checkImage() }, 50);
        }

        // 四辺形の描画
        const drawRect = (location) => {
            rectCvs.width = contentWidth;
            rectCvs.height = contentHeight;
            drawLine(location.topLeftCorner, location.topRightCorner);
            drawLine(location.topRightCorner, location.bottomRightCorner);
            drawLine(location.bottomRightCorner, location.bottomLeftCorner);
            drawLine(location.bottomLeftCorner, location.topLeftCorner);
            console.log(location.topLeftCorner, location.topRightCorner);
        }

        // 線の描画
        const drawLine = (begin, end) => {
            rectCtx.lineWidth = 4;
            rectCtx.strokeStyle = "red";
            rectCtx.beginPath();
            rectCtx.moveTo(begin.x, begin.y);
            rectCtx.lineTo(end.x, end.y);
            rectCtx.stroke();
        }



    </script>
</x-app-layout>

