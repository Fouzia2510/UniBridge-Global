<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            padding: 60px;
            border: 15px solid #4F46E5;
        }
        .title {
            font-size: 42px;
            color: #4F46E5;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 40px;
        }
        .presented {
            font-size: 18px;
            color: #374151;
        }
        .student-name {
            font-size: 34px;
            color: #1F2937;
            font-weight: bold;
            margin: 20px 0;
            border-bottom: 2px solid #4F46E5;
            display: inline-block;
            padding-bottom: 10px;
        }
        .course {
            font-size: 20px;
            color: #374151;
            margin: 20px 0;
        }
        .course-title {
            font-weight: bold;
            color: #4F46E5;
        }
        .date {
            margin-top: 40px;
            font-size: 14px;
            color: #6B7280;
        }
        .footer {
            margin-top: 60px;
            font-size: 12px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>
    <div class="title">Certificate of Completion</div>
    <div class="subtitle">AI Study Abroad Consultancy Platform</div>

    <div class="presented">This certifies that</div>
    <div class="student-name">{{ $studentName }}</div>

    <div class="course">
        has successfully completed the course<br>
        <span class="course-title">{{ $courseTitle }}</span>
    </div>

    <div class="date">Completed on {{ $completionDate }}</div>

    <div class="footer">This certificate is issued electronically by the AI Study Abroad Consultancy Platform.</div>
</body>
</html>