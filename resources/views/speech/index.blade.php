<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcripción de Voz en Tiempo Real</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://aka.ms/csspeech/jsbrowserpackageraw"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Transcripción de Voz en Tiempo Real con Azure Speech</h2>

    <!-- Botones -->
    <button id="startRecognition" class="btn btn-success">Iniciar Reconocimiento</button>
    <button id="stopRecognition" class="btn btn-danger d-none">Detener</button>
    <button id="clearText" class="btn btn-secondary d-none">Limpiar</button>

    <!-- Campo de Transcripción -->
    <h4 class="mt-3">Texto transcrito:</h4>
    <div class="border p-3 bg-white" id="resultText">Esperando transcripción...</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let resultText = document.getElementById("resultText");
        let startButton = document.getElementById("startRecognition");
        let stopButton = document.getElementById("stopRecognition");
        let clearButton = document.getElementById("clearText");

        let speechConfig = SpeechSDK.SpeechConfig.fromSubscription("{{ env('AZURE_SPEECH_KEY') }}", "{{ env('AZURE_REGION') }}");
        speechConfig.speechRecognitionLanguage = "es-ES";

        let audioConfig = SpeechSDK.AudioConfig.fromDefaultMicrophoneInput();
        let recognizer = new SpeechSDK.SpeechRecognizer(speechConfig, audioConfig);

        let finalText = ""; // Acumulador de texto final

        startButton.addEventListener("click", function () {
            resultText.innerText = "Esperando permiso para el micrófono...";

            navigator.mediaDevices.getUserMedia({ audio: true }).then(() => {
                resultText.innerText = "Escuchando...";
                startButton.classList.add("d-none");
                stopButton.classList.remove("d-none");
                clearButton.classList.remove("d-none");

                recognizer.startContinuousRecognitionAsync();

                recognizer.recognizing = function (s, e) {
                    resultText.innerText = finalText + " " + e.result.text; // Muestra la transcripción temporal sin sobrescribir el texto acumulado
                };

                recognizer.recognized = function (s, e) {
                    if (e.result.reason === SpeechSDK.ResultReason.RecognizedSpeech) {
                        finalText += " " + e.result.text; // Agrega el texto reconocido al final
                        resultText.innerText = finalText; // Muestra el texto acumulado correctamente
                    }
                };

            }).catch(err => {
                resultText.innerText = "Error: No se pudo acceder al micrófono.";
                console.error("Error accediendo al micrófono:", err);
            });
        });

        stopButton.addEventListener("click", function () {
            recognizer.stopContinuousRecognitionAsync(() => {
                stopButton.classList.add("d-none");
                startButton.classList.remove("d-none");
            });
        });

        clearButton.addEventListener("click", function () {
            finalText = "";
            resultText.innerText = "Esperando transcripción...";
            clearButton.classList.add("d-none");
        });
    });
</script>
</body>
</html>
