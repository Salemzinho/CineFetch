<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DvdCrawlerController extends Controller
{
    public function showDvdList(Request $request)
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

        $search = $request->input('search');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');

        if($search) {
            $dvds = array_filter($dvds, function ($dvd) use ($search) {
                return stripos($dvd['title'], $search) !== false;
            });
        }

        if($min_price || $max_price) {
            $dvds = array_filter($dvds, function ($dvd) use ($min_price, $max_price) {
                $price = floatval(str_replace(['R$', ','], ['', '.'], $dvd['price']));

                if($min_price && $price < $min_price) {
                    return false;
                }

                if($max_price && $price > $max_price) {
                    return false;
                }

                return true;
            });
        }

        if($request->input('sort') === 'asc') {
            usort($dvds, function ($a, $b) {
                return floatval(str_replace(['R$', ' '], '', $a['price'])) <=> floatval(str_replace(['R$', ' '], '', $b['price']));
            });
        } elseif($request->input('sort') === 'desc') {
            usort($dvds, function ($a, $b) {
                return floatval(str_replace(['R$', ' '], '', $b['price'])) <=> floatval(str_replace(['R$', ' '], '', $a['price']));
            });
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
