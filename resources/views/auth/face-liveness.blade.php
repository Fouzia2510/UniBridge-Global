<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>UniBridge - Face Verification</title>

    @vite([
        'resources/css/app.css',
        'resources/js/face-liveness.js'
    ])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
        }

        body {
            min-height: 100vh;
        }

        .face-page {
            width: 100%;
            min-height: 100vh;
            padding: 40px 16px;
        }

        .face-container {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
        }

        .face-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand {
            display: inline-block;
            margin-bottom: 12px;
            color: #4f46e5;
            font-size: 14px;
            font-weight: 700;
        }

        .page-title {
            margin: 0;
            color: #0f172a;
            font-size: 32px;
            line-height: 1.2;
            font-weight: 800;
        }

        .page-description {
            margin: 12px auto 0;
            max-width: 560px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
        }

        .face-card {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        #camera-box {
            position: relative;
            display: none;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #020617;
            border-radius: 14px;
        }

        #face-camera {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .face-outline {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 180px;
            height: 240px;
            transform: translate(-50%, -50%);
            border: 2px solid rgba(255,255,255,0.85);
            border-radius: 50%;
            pointer-events: none;
        }

        .instruction-box {
            position: absolute;
            top: 16px;
            left: 16px;
            right: 16px;
            text-align: center;
            pointer-events: none;
        }

        #face-instruction {
            display: inline-block;
            padding: 9px 18px;
            background: rgba(0,0,0,0.65);
            color: #ffffff;
            border-radius: 9px;
            font-size: 17px;
            line-height: 1.3;
            font-weight: 700;
        }

        .status-area {
            text-align: center;
            margin-top: 22px;
        }

        #face-status {
            color: #475569;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 600;
        }

        .progress {
            margin-top: 10px;
            color: #64748b;
            font-size: 14px;
        }

        #face-progress {
            color: #4f46e5;
            font-weight: 700;
        }

        .steps-title {
            margin-top: 28px;
            margin-bottom: 12px;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.4;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .step {
            min-width: 0;
            min-height: 100px;
            padding: 16px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            text-align: center;
        }

        .step-circle {
            width: 42px;
            height: 42px;
            margin: 0 auto;
            border-radius: 50%;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4338ca;
            font-size: 12px;
            line-height: 1;
            font-weight: 800;
            overflow: hidden;
        }

        .step-name {
            margin-top: 10px;
            color: #334155;
            font-size: 13px;
            line-height: 1.3;
            font-weight: 700;
        }

        #start-face-camera {
            display: block;
            width: 100%;
            margin-top: 26px;
            padding: 13px 18px;
            border: 0;
            border-radius: 10px;
            background: #4f46e5;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            font-weight: 700;
            cursor: pointer;
        }

        #start-face-camera:hover {
            background: #4338ca;
        }

        #start-face-camera:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .privacy {
            margin-top: 18px;
            padding: 14px;
            border: 1px solid #e0e7ff;
            border-radius: 10px;
            background: #eef2ff;
        }

        .privacy p {
            margin: 0;
            color: #475569;
            font-size: 12px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
        }

        @media (max-width: 640px) {

            .face-page {
                padding: 25px 12px;
            }

            .face-card {
                padding: 16px;
            }

            .page-title {
                font-size: 27px;
            }

            .steps {
                grid-template-columns: repeat(2, 1fr);
            }

            .step {
                min-height: 90px;
            }

            .face-outline {
                width: 140px;
                height: 190px;
            }
        }
    </style>
</head>

<body>

    <main class="face-page">

        <div class="face-container">

            <header class="face-header">

                <div class="brand">
                    UniBridge Security
                </div>

                <h1 class="page-title">
                    Face Verification
                </h1>

                <p class="page-description">
                    Follow the instructions and move your head when asked.
                </p>

            </header>


            <section class="face-card">

                <div id="camera-box">

                    <video
                        id="face-camera"
                        autoplay
                        muted
                        playsinline
                    ></video>

                    <div class="face-outline"></div>

                    <div class="instruction-box">

                        <div id="face-instruction">
                            Look UP
                        </div>

                    </div>

                </div>


                <div class="status-area">

                    <div id="face-status">
                        Camera is not started.
                    </div>

                    <div class="progress">
                        Progress:
                        <span id="face-progress">
                            0 / 4
                        </span>
                    </div>

                </div>


                <div class="steps-title">
                    Verification steps
                </div>


                <div class="steps">

                    <div class="step">

                        <div class="step-circle">
                            UP
                        </div>

                        <div class="step-name">
                            Look Up
                        </div>

                    </div>


                    <div class="step">

                        <div class="step-circle">
                            LEFT
                        </div>

                        <div class="step-name">
                            Look Left
                        </div>

                    </div>


                    <div class="step">

                        <div class="step-circle">
                            RIGHT
                        </div>

                        <div class="step-name">
                            Look Right
                        </div>

                    </div>


                    <div class="step">

                        <div class="step-circle">
                            DOWN
                        </div>

                        <div class="step-name">
                            Look Down
                        </div>

                    </div>

                </div>


                <button
                    id="start-face-camera"
                    type="button"
                >
                    Start Face Verification
                </button>


                <div class="privacy">

                    <p>
                        Face verification checks that a real person is
                        present and following the requested movements.
                        UniBridge does not upload or store your camera image.
                    </p>

                </div>

            </section>


            <div class="footer">
                UniBridge Security Verification
            </div>

        </div>

    </main>

</body>
</html>