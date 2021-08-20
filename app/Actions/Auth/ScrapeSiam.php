<?php

namespace App\Actions\Auth;

use Goutte\Client;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class ScrapeSiam {
    use AsAction;

    /**
     * @param Request $request
     * @return array|string[]
     */
    public function handle (Request $request)
    {
        $authHeader = base64_decode(explode(' ', $request->header('Authorization'))[1]);

        $nim = explode(':', $authHeader)[0];
        $password = explode(':', $authHeader)[1];

        $cl = new Client();

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/');
        $form = $cr->selectButton('Masuk')->form();
        $cr = $cl->submit($form, array('username' => $nim, 'password' => $password));

        $cek = $cr->filter('small.error-code')->each(function ($result) {
            return $result->text();
        });

        if (isset($cek[0])) {
            $response = [
                'message' => 'NIM atau password salah'
            ];
        } else {
            $data = $cr->filter('div[class="bio-info"] > div')->each(function ($result) {
                return $result->text();
            });

            // Create the token payload
            $payload = json_encode([
                'AUTHORITY' => [
                  'PASSWD' => 1
                ],
                "nim" => $data[0],
                "nama" => $data[1],
                "fakultas" => substr($data[2], 19),
                "jurusan" => substr($data[3], 7),
                "prodi" => substr($data[4], 13)
            ]);

            $data = base64_encode($payload);

            $response = [
                'message' => 'success',
                'data' => $data
            ];
        }

        return $response;
    }
}
