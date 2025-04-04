<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Signature for PDF</title>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
</head>
<body>
    <h1>Digital Signature for PDF</h1>
    <input type="file" id="pdf-upload" accept="application/pdf" />
    <br><br>
    <canvas id="signature-pad" style="border: 1px solid black;"></canvas>
    <br>
    <button onclick="clearSignature()">Clear Signature</button>
    <br><br>
    <button onclick="addSignatureToPdf()">Add Signature to PDF</button>
    <br><br>
    <a id="download-link" style="display: none" href="#" download="signed-pdf.pdf">Download Signed PDF</a>

    <script>
        const pdfUpload = document.getElementById('pdf-upload');
        const signaturePad = document.getElementById('signature-pad');
        const ctx = signaturePad.getContext('2d');
        let signatureDataUrl = null;
        let uploadedPdf = null;

        function resizeCanvas() {
            signaturePad.width = window.innerWidth;
            signaturePad.height = window.innerHeight * 0.7;
        }

        pdfUpload.addEventListener('change', handlePdfUpload);

        let isDrawing = false;

        signaturePad.addEventListener('mousedown', startDrawing);
        signaturePad.addEventListener('mousemove', drawSignature);
        signaturePad.addEventListener('mouseup', stopDrawing);
        signaturePad.addEventListener('mouseleave', stopDrawing);

        signaturePad.addEventListener('touchstart', startDrawing);
        signaturePad.addEventListener('touchmove', drawSignature);
        signaturePad.addEventListener('touchend', stopDrawing);
        signaturePad.addEventListener('touchcancel', stopDrawing);

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            const rect = signaturePad.getBoundingClientRect();
            const offsetX = e.touches ? e.touches[0].clientX - rect.left : e.offsetX;
            const offsetY = e.touches ? e.touches[0].clientY - rect.top : e.offsetY;
            ctx.moveTo(offsetX, offsetY);
        }

        function drawSignature(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const rect = signaturePad.getBoundingClientRect();
            const offsetX = e.touches ? e.touches[0].clientX - rect.left : e.offsetX;
            const offsetY = e.touches ? e.touches[0].clientY - rect.top : e.offsetY;
            ctx.lineTo(offsetX, offsetY);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
            signatureDataUrl = signaturePad.toDataURL();
        }

        function clearSignature() {
            ctx.clearRect(0, 0, signaturePad.width, signaturePad.height);
            signatureDataUrl = null;
        }

        async function addSignatureToPdf() {
            if (!uploadedPdf) {
                alert("Please upload a PDF file first.");
                return;
            }

            if (!signatureDataUrl) {
                alert("Please draw a signature.");
                return;
            }

            const pdfDoc = await PDFLib.PDFDocument.load(uploadedPdf);
            const pages = pdfDoc.getPages();
            const lastPage = pages[pages.length - 1];
            const { width, height } = lastPage.getSize();

            const signatureImageBytes = await fetch(signatureDataUrl).then(res => res.arrayBuffer());
            const signatureImage = await pdfDoc.embedPng(signatureImageBytes);

            const signatureWidth = 200;
            const signatureHeight = 150;

            const marginBottom = 170;
            const approximateTextPositionY = height - marginBottom;

            const yPosition = approximateTextPositionY - signatureHeight - 10;
            const xPosition = width - signatureWidth - 50;

            lastPage.drawImage(signatureImage, {
                x: xPosition,
                y: yPosition,
                width: signatureWidth,
                height: signatureHeight,
            });

            const pdfBytes = await pdfDoc.save();

            const formData = new FormData();
            formData.append('pdf', new Blob([pdfBytes], { type: 'application/pdf' }));

            fetch('save_pdf.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const downloadLink = document.getElementById('download-link');
                    downloadLink.href = data.filePath;
                    downloadLink.style.display = 'inline-block';
                } else {
                    alert('Failed to save PDF');
                }
            });
        }

        function handlePdfUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    uploadedPdf = reader.result;
                };
                reader.readAsArrayBuffer(file);
            }
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        ctx.lineWidth = 5;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
    </script>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Signature for PDF</title>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
</head>
<body>
    <h1>Digital Signature for PDF</h1>
    <canvas id="signature-pad" style="border: 1px solid black;"></canvas>
    <br>
    <button onclick="clearSignature()">Clear Signature</button>
    <br><br>
    <button onclick="addSignatureToPdf()">Add Signature to PDF</button>
    <br><br>
    <a id="download-link" style="display: none" href="#" download="signed-pdf.pdf">Download Signed PDF</a>

    <script>
        const signaturePad = document.getElementById('signature-pad');
        const ctx = signaturePad.getContext('2d');
        let signatureDataUrl = null;
        let uploadedPdf = null;

        function resizeCanvas() {
            signaturePad.width = window.innerWidth;
            signaturePad.height = window.innerHeight * 0.7;
        }

        // Automatically create a blank PDF and load it into the PDF document
        (async function() {
            const pdfDoc = await PDFLib.PDFDocument.create(); // Create a new empty PDF
            pdfDoc.addPage([600, 800]); // Add a blank page to the PDF
            uploadedPdf = await pdfDoc.save(); // Save the blank PDF as an array of bytes
        })();

        let isDrawing = false;

        signaturePad.addEventListener('mousedown', startDrawing);
        signaturePad.addEventListener('mousemove', drawSignature);
        signaturePad.addEventListener('mouseup', stopDrawing);
        signaturePad.addEventListener('mouseleave', stopDrawing);

        signaturePad.addEventListener('touchstart', startDrawing);
        signaturePad.addEventListener('touchmove', drawSignature);
        signaturePad.addEventListener('touchend', stopDrawing);
        signaturePad.addEventListener('touchcancel', stopDrawing);

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            const rect = signaturePad.getBoundingClientRect();
            const offsetX = e.touches ? e.touches[0].clientX - rect.left : e.offsetX;
            const offsetY = e.touches ? e.touches[0].clientY - rect.top : e.offsetY;
            ctx.moveTo(offsetX, offsetY);
        }

        function drawSignature(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const rect = signaturePad.getBoundingClientRect();
            const offsetX = e.touches ? e.touches[0].clientX - rect.left : e.offsetX;
            const offsetY = e.touches ? e.touches[0].clientY - rect.top : e.offsetY;
            ctx.lineTo(offsetX, offsetY);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
            signatureDataUrl = signaturePad.toDataURL();
        }

        function clearSignature() {
            ctx.clearRect(0, 0, signaturePad.width, signaturePad.height);
            signatureDataUrl = null;
        }

        async function addSignatureToPdf() {
            if (!uploadedPdf) {
                alert("No PDF available.");
                return;
            }

            if (!signatureDataUrl) {
                alert("Please draw a signature.");
                return;
            }

            const pdfDoc = await PDFLib.PDFDocument.load(uploadedPdf); // Load the blank PDF
            const pages = pdfDoc.getPages();
            const lastPage = pages[pages.length - 1];
            const { width, height } = lastPage.getSize();

            const signatureImageBytes = await fetch(signatureDataUrl).then(res => res.arrayBuffer());
            const signatureImage = await pdfDoc.embedPng(signatureImageBytes);

            const signatureWidth = 200;
            const signatureHeight = 150;

            const marginBottom = 170;
            const approximateTextPositionY = height - marginBottom;

            const yPosition = approximateTextPositionY - signatureHeight - 10;
            const xPosition = width - signatureWidth - 50;

            lastPage.drawImage(signatureImage, {
                x: xPosition,
                y: yPosition,
                width: signatureWidth,
                height: signatureHeight,
            });

            const pdfBytes = await pdfDoc.save();

            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);
            const downloadLink = document.getElementById('download-link');
            downloadLink.href = url;
            downloadLink.style.display = 'inline-block';
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        ctx.lineWidth = 5;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
    </script>

    <script>
        async function addSignatureToPdf() {
    if (!uploadedPdf) {
        alert("No PDF available.");
        return;
    }

    if (!signatureDataUrl) {
        alert("Please draw a signature.");
        return;
    }

    const pdfDoc = await PDFLib.PDFDocument.load(uploadedPdf); // Load the blank PDF
    const pages = pdfDoc.getPages();
    const lastPage = pages[pages.length - 1];
    const { width, height } = lastPage.getSize();

    const signatureImageBytes = await fetch(signatureDataUrl).then(res => res.arrayBuffer());
    const signatureImage = await pdfDoc.embedPng(signatureImageBytes);

    const signatureWidth = 200;
    const signatureHeight = 150;

    const marginBottom = 170;
    const approximateTextPositionY = height - marginBottom;

    const yPosition = approximateTextPositionY - signatureHeight - 10;
    const xPosition = width - signatureWidth - 50;

    lastPage.drawImage(signatureImage, {
        x: xPosition,
        y: yPosition,
        width: signatureWidth,
        height: signatureHeight,
    });

    const pdfBytes = await pdfDoc.save();

    // Create a FormData object to send the PDF to PHP
    const formData = new FormData();
    formData.append('pdf', new Blob([pdfBytes], { type: 'application/pdf' }), 'signed-pdf.pdf');

    // Send the signed PDF to the server using fetch
    fetch('save_pdf.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const downloadLink = document.getElementById('download-link');
            downloadLink.href = data.filePath;
            downloadLink.style.display = 'inline-block';
        } else {
            alert('Failed to save PDF');
        }
    });
}

    </script>
</body>
</html>
