<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Foundation\Application;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// use GuzzleHttp\Exception\RequestException;

class AcessTokenContoller extends Controller
{
    public function createAccessToken(Request $request)
    {
        $inputs = $request->all();

        //Set default scope with full access
        if (!isset($inputs['scope']) || empty($inputs['scope'])) {
            $inputs['scope'] = "*";
        }

        $countHost = 0;
        $requestHosts = function () use (&$requestHosts, $request, $inputs, &$countHost) {

            $urlHost = env('API_AUTH_' . $countHost, '');

            $canNext = false;
            # check if has next host
            if (env('API_AUTH_' . ($countHost + 1), FALSE)) {
                $canNext = true;
            }
            // dd($urlHost);
            if ($urlHost === '') {
                $urlHost = env('API_URL', '');
                $tokenRequest = $request->create('/oauth/token', 'post', $inputs);
                // $response = app()->dispatch($tokenRequest);
                $response = dispatch($tokenRequest);


                $httpCode = $response->getStatusCode();
                // Log::info($httpCode);
                switch ($httpCode) {
                    case 400:
                        $content = '{"error": "invalid_grant", "error_description": "The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client.", "hint": "", "message": "As credenciais do usuário estão incorretas."}';
                        break;

                    default:
                        $content = $response->getContent();
                        break;
                }
                $logicaUrl = env('LOGICA_URL', '');
                $contentHost = '{"host": {"api_url": "'.$urlHost.'", "logica_url": "'.$logicaUrl.'"},"auth": '.$content.'}';
            } else {
                $client = new Client([
                    'base_uri' => $urlHost,
                    'timeout'  => 3.0,
                ]);
                try {
                    $response = $client->request('POST', '/accessToken', [
                        'form_params' => $inputs
                    ]);
                    // echo"asdasd";
                    // dd($response);
                } catch (RequestException $e) {
                    $response = $e->getMessage();
                    // echo"asdasd2222";
                    // dd($response);
                }

                $content = $response->getBody()->getContents();
                $httpCode = $response->getStatusCode();
                $contentHost = $content;
            }
            $resp = new Response(
                $contentHost,
                $httpCode,
                array('content-type' => 'application/json')
            );

            if (!$canNext) {
                return $resp;
            } else if ($resp->getStatusCode() == 200) {
                return $resp;
            } else {
                $countHost++;
                return $requestHosts();
            }
        };

        $resp = $requestHosts();
        // forward the request to the oauth token request endpoint
        return $resp;
    }
}
