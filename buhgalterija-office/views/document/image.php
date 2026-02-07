<!DOCTYPE html>
<head>
    <title>Document Image View</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }
        .image-container {
            max-width: 100%;
            max-height: 100%;           
            overflow: auto;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .image-container img {
            display: block;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="/document/file/<?= $document->id ?>" alt="<?= htmlspecialchars($document->filename) ?>" />
    </div>
</body>
</html>