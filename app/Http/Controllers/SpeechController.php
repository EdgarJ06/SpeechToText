<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpeechController extends Controller
{
    public function index()
    {
        return view('speech.index');
    }

    public function transcribirAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3|max:10240', // Máximo 10MB
        ]);

        $audioFile = $request->file('audio');
        $audioData = file_get_contents($audioFile->getPathname());

        $client = new Client();
        $apiKey = env('AZURE_SPEECH_KEY');
        $endpoint = env('AZURE_ENDPOINT');

        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $apiKey,
                    'Content-Type' => 'audio/wav',
                    'Accept' => 'application/json',
                ],
                'body' => $audioData,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['DisplayText'])) {
                return back()->with('success', 'Transcripción exitosa:')->with('text', $responseData['DisplayText']);
            } else {
                return back()->with('error', 'No se pudo transcribir el audio.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error en la transcripción: ' . $e->getMessage());
        }
    }
}
