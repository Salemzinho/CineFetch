<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class DvdCrawlerController extends Controller
{
    public function showDvdList()
    {
        $urls = [
            'https://videoperola.com.br/produtos/blu-ray-maligno/',
            'https://videoperola.com.br/produtos/blu-ray-a-forca/',
            'https://videoperola.com.br/produtos/blu-ray-a-maldicao-da-chorona/',
            'https://videoperola.com.br/produtos/blu-ray-it-a-coisa/',
            'https://videoperola.com.br/produtos/blu-ray-annabelle-2-a-criacao-do-mal/',
            'https://videoperola.com.br/produtos/blu-ray-premonicao-5/',
            'https://videoperola.com.br/produtos/blu-ray-doutor-sono/',
            'https://videoperola.com.br/produtos/blu-ray-casamento-sangrento/',
            'https://videoperola.com.br/produtos/blu-ray-annabelle-3-de-volta-para-a-casa/',
            'https://videoperola.com.br/produtos/blu-ray-a-lenda-de-candyman/',
            'https://videoperola.com.br/produtos/blu-ray-parasita/',
            'https://videoperola.com.br/produtos/blu-ray-panico-2022/',
            'https://videoperola.com.br/produtos/blu-ray-panico/',
            'https://videoperola.com.br/produtos/blu-ray-premonicao-2/',
            'https://videoperola.com.br/produtos/blu-ray-seven-os-sete-crimes-capitais/',
        ];

        $client = new Client();
        $dvds = [];

        foreach($urls as $url) {
            $price = $this->getMoviePrice($client, $url);
            $cover = $this->getMovieCover($client, $url);
            $dvds[] = [
                'url' => $url,
                'price' => $price,
                'cover' => $cover,
                'title' => $this->extractTitle($url)
            ];
        }

        return view('dvds', ['dvds' => $dvds]);
    }

    private function getMoviePrice(Client $client, $url)
    {
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0'
                ]
            ]);

            $html = $response->getBody()->getContents();

            if(preg_match('/"price_short":"([^"]+)"/', $html, $matches)) {
                return $matches[1];
            } else {
                return "Preço não encontrado";
            }
        } catch (\Exception $e) {
            return "Erro ao acessar {$url}: {$e->getMessage()}";
        }
    }

    private function getMovieCover(Client $client, $url)
    {
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0'
                ]
            ]);

            $html = $response->getBody()->getContents();

            if(preg_match('/<meta property="og:image:secure_url" content="([^"]+)"/', $html, $matches)) {
                return $matches[1];
            } else {
                return 'https://via.placeholder.com/640x360.png?text=Imagem+n%C3%A3o+encontrada';
            }
        } catch (\Exception $e) {
            return 'https://via.placeholder.com/640x360.png?text=Erro+de+conex%C3%A3o';
        }
    }

    private function extractTitle($url)
    {
        $parts = explode('/', trim($url, '/'));
        return str_replace('-', ' ', ucfirst(end($parts)));
    }
}
