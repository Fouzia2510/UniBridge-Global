import {
    FaceLandmarker,
    FilesetResolver,
} from '@mediapipe/tasks-vision';

let faceLandmarker = null;
let video = null;
let animationFrame = null;

const sequence = ['up', 'left', 'right', 'down'];

let currentStep = 0;
let stableFrames = 0;
let completed = false;

const statusEl = document.getElementById('face-status');
const instructionEl = document.getElementById('face-instruction');
const progressEl = document.getElementById('face-progress');
const startButton = document.getElementById('start-face-camera');
const cameraBox = document.getElementById('camera-box');


function setStatus(message) {
    if (statusEl) {
        statusEl.textContent = message;
    }
}


function setInstruction() {

    if (!instructionEl) {
        return;
    }

    const labels = {
        up: 'Look UP',
        left: 'Look LEFT',
        right: 'Look RIGHT',
        down: 'Look DOWN',
    };

    if (currentStep >= sequence.length) {

        instructionEl.textContent =
            'Face verification successful';

        return;
    }

    instructionEl.textContent =
        labels[sequence[currentStep]];
}


function updateProgress() {

    if (!progressEl) {
        return;
    }

    progressEl.textContent =
        `${Math.min(currentStep, sequence.length)} / ${sequence.length}`;
}


async function createFaceLandmarker() {

    setStatus('Loading face detection...');

    const vision =
        await FilesetResolver.forVisionTasks(
            'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.22/wasm'
        );

    faceLandmarker =
        await FaceLandmarker.createFromOptions(
            vision,
            {
                baseOptions: {
                    modelAssetPath:
                        'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task',

                    delegate: 'GPU',
                },

                runningMode: 'VIDEO',

                numFaces: 1,

                minFaceDetectionConfidence: 0.5,

                minFacePresenceConfidence: 0.5,

                minTrackingConfidence: 0.5,
            }
        );

    setStatus('Face detection ready.');
}


async function startCamera() {

    try {

        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia
        ) {
            throw new Error(
                'Camera access is not available in this browser.'
            );
        }

        if (startButton) {
            startButton.disabled = true;
        }

        await createFaceLandmarker();

        setStatus(
            'Requesting camera permission...'
        );

        const stream =
            await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',

                    width: {
                        ideal: 640,
                    },

                    height: {
                        ideal: 480,
                    },
                },

                audio: false,
            });

        if (!video) {
            throw new Error(
                'Camera video element was not found.'
            );
        }

        video.srcObject = stream;

        await video.play();

        if (cameraBox) {
            cameraBox.classList.remove('hidden');
        }

        currentStep = 0;
        stableFrames = 0;
        completed = false;

        updateProgress();
        setInstruction();

        setStatus(
            'Camera active. Follow the instruction.'
        );

        detectFace();

    } catch (error) {

        console.error(
            'UniBridge Face Liveness Error:',
            error
        );

        setStatus(
            error?.message ||
            'Unable to start camera. Please allow camera access and try again.'
        );

        if (startButton) {
            startButton.disabled = false;
        }
    }
}


function getFacePoints(landmarks) {

    const nose = landmarks[1];

    const leftEye = landmarks[33];

    const rightEye = landmarks[263];

    if (
        !nose ||
        !leftEye ||
        !rightEye
    ) {
        return null;
    }

    return {
        nose,
        leftEye,
        rightEye,
    };
}


function detectDirection(landmarks) {

    const points =
        getFacePoints(landmarks);

    if (!points) {
        return null;
    }

    const {
        nose,
        leftEye,
        rightEye,
    } = points;

    const eyeCenterX =
        (leftEye.x + rightEye.x) / 2;

    const eyeCenterY =
        (leftEye.y + rightEye.y) / 2;

    const horizontalOffset =
        nose.x - eyeCenterX;

    const verticalOffset =
        nose.y - eyeCenterY;


    if (verticalOffset < -0.045) {
        return 'up';
    }

    if (verticalOffset > 0.055) {
        return 'down';
    }

    if (horizontalOffset < -0.055) {
        return 'left';
    }

    if (horizontalOffset > 0.055) {
        return 'right';
    }

    return null;
}


function markDirection(direction) {

    if (completed) {
        return;
    }

    const expected =
        sequence[currentStep];

    if (direction === expected) {
        stableFrames += 1;
    } else {
        stableFrames = 0;
    }

    if (stableFrames >= 6) {

        stableFrames = 0;

        currentStep += 1;

        updateProgress();

        if (
            currentStep >=
            sequence.length
        ) {
            completeVerification();
            return;
        }

        setInstruction();

        setStatus(
            'Good. Next movement.'
        );
    }
}


function completeVerification() {

    completed = true;

    setInstruction();

    updateProgress();

    setStatus(
        'Face liveness verification successful.'
    );

    if (
        video &&
        video.srcObject
    ) {
        video.srcObject
            .getTracks()
            .forEach(
                track => track.stop()
            );
    }

    if (animationFrame) {
        cancelAnimationFrame(
            animationFrame
        );

        animationFrame = null;
    }

    document.dispatchEvent(
        new CustomEvent(
            'face-liveness-success'
        )
    );
}


function detectFace() {

    if (
        !faceLandmarker ||
        !video ||
        video.readyState < 2 ||
        completed
    ) {
        return;
    }

    const now =
        performance.now();

    const result =
        faceLandmarker.detectForVideo(
            video,
            now
        );

    if (
        !result.faceLandmarks ||
        result.faceLandmarks.length === 0
    ) {

        setStatus(
            'No face detected. Look at the camera.'
        );

        stableFrames = 0;

    } else if (
        result.faceLandmarks.length > 1
    ) {

        setStatus(
            'Please make sure only one face is visible.'
        );

        stableFrames = 0;

    } else {

        setStatus(
            'Face detected. Follow the instruction.'
        );

        const landmarks =
            result.faceLandmarks[0];

        const direction =
            detectDirection(
                landmarks
            );

        markDirection(
            direction
        );
    }

    animationFrame =
        requestAnimationFrame(
            detectFace
        );
}


function initialize() {

    video =
        document.getElementById(
            'face-camera'
        );

    if (!video) {
        return;
    }

    if (startButton) {
        startButton.addEventListener(
            'click',
            startCamera
        );
    }

    updateProgress();

    setInstruction();
}


if (
    document.readyState ===
    'loading'
) {

    document.addEventListener(
        'DOMContentLoaded',
        initialize
    );

} else {

    initialize();
}